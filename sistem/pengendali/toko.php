<?php

declare(strict_types=1);

function handle_store_post(): void
{
    $action = (string) ($_POST['action'] ?? '');
    $isCartAjax = $action === 'add_to_cart' && (
        (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT'])
            && str_contains(strtolower((string) $_SERVER['HTTP_ACCEPT']), 'application/json'))
    );

    // AJAX add-to-cart must return JSON even when the CSRF token has expired.
    if ($isCartAjax) {
        $submittedToken = $_POST['_csrf'] ?? '';
        if (!is_string($submittedToken) || !hash_equals(csrf_token(), $submittedToken)) {
            http_response_code(419);
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store');
            echo json_encode([
                'success' => false,
                'message' => 'Sesi halaman sudah kedaluwarsa. Muat ulang halaman, lalu coba tambahkan buku lagi.',
                'reload' => true,
            ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }
    } else {
        verify_csrf();
    }

    try {
        $pdo = db();
        switch ($action) {
            case 'register':
                $name = trim((string) ($_POST['name'] ?? ''));
                $email = strtolower(trim((string) ($_POST['email'] ?? '')));
                $phone = trim((string) ($_POST['phone'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');

                if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
                    throw new RuntimeException('Lengkapi nama, email yang valid, dan kata sandi minimal 8 karakter.');
                }

                $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $check->execute([$email]);
                if ($check->fetch()) {
                    throw new RuntimeException('Email sudah terdaftar. Silakan gunakan email lain atau login.');
                }

                $insert = $pdo->prepare(
                    'INSERT INTO users (name, email, phone, password_hash, role, status)
                     VALUES (?, ?, ?, ?, "user", "active")'
                );
                $insert->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
                login_user(['id' => (int) $pdo->lastInsertId()]);
                flash('success', 'Akun Lumière Books berhasil dibuat. Selamat berbelanja!');
                redirect(url('home'));

            case 'login':
                $email = strtolower(trim((string) ($_POST['email'] ?? '')));
                $password = (string) ($_POST['password'] ?? '');
                $statement = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
                $statement->execute([$email]);
                $user = $statement->fetch();

                if (!$user || !password_verify($password, $user['password_hash']) || $user['status'] !== 'active') {
                    throw new RuntimeException('Email atau kata sandi salah, atau akun tidak aktif.');
                }

                login_user($user);
                flash('success', 'Berhasil login. Selamat datang kembali!');
                redirect($user['role'] === 'admin' ? admin_url() : url('home'));

            case 'logout':
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'],
                        $params['secure'], $params['httponly']);
                }
                session_destroy();
                redirect(url('home'));

            case 'add_to_cart':
                $user = current_user();
                if (!$user) {
                    throw new RuntimeException('Silakan login terlebih dahulu untuk menambahkan buku ke keranjang.');
                }
                $bookId = (int) ($_POST['book_id'] ?? 0);
                $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
                $bookQuery = $pdo->prepare('SELECT id, title, stock FROM books WHERE id = ? AND status = "active"');
                $bookQuery->execute([$bookId]);
                $book = $bookQuery->fetch();

                if (!$book || (int) $book['stock'] < 1) {
                    throw new RuntimeException('Buku tidak tersedia atau stok sedang kosong.');
                }

                $existing = $pdo->prepare('SELECT quantity FROM cart_items WHERE user_id = ? AND book_id = ?');
                $existing->execute([$user['id'], $bookId]);
                $current = (int) ($existing->fetchColumn() ?: 0);
                $newQuantity = min((int) $book['stock'], $current + $quantity);

                $save = $pdo->prepare(
                    'INSERT INTO cart_items (user_id, book_id, quantity)
                     VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)'
                );
                $save->execute([$user['id'], $bookId, $newQuantity]);
                $newCartCount = cart_count();
                if ($isCartAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    header('Cache-Control: no-store');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Buku berhasil ditambahkan ke keranjang!',
                        'cart_count' => $newCartCount,
                        'book_title' => (string) $book['title'],
                    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
                    exit;
                }
                flash('success', 'Buku berhasil ditambahkan ke keranjang.');
                redirect(url('cart'));

            case 'update_cart':
                $user = require_login();
                $cartId = (int) ($_POST['cart_id'] ?? 0);
                $quantity = (int) ($_POST['quantity'] ?? 1);

                if ($quantity <= 0) {
                    $delete = $pdo->prepare('DELETE FROM cart_items WHERE id = ? AND user_id = ?');
                    $delete->execute([$cartId, $user['id']]);
                } else {
                    $update = $pdo->prepare(
                        'UPDATE cart_items ci
                         INNER JOIN books b ON b.id = ci.book_id
                         SET ci.quantity = LEAST(?, b.stock)
                         WHERE ci.id = ? AND ci.user_id = ? AND b.stock > 0'
                    );
                    $update->execute([$quantity, $cartId, $user['id']]);
                }
                flash('success', 'Keranjang berhasil diperbarui.');
                redirect(url('cart'));

            case 'remove_cart':
                $user = require_login();
                $delete = $pdo->prepare('DELETE FROM cart_items WHERE id = ? AND user_id = ?');
                $delete->execute([(int) ($_POST['cart_id'] ?? 0), $user['id']]);
                flash('success', 'Buku dihapus dari keranjang.');
                redirect(url('cart'));

            case 'checkout':
                $user = require_login();
                $recipient = trim((string) ($_POST['recipient_name'] ?? $user['name']));
                $phone = trim((string) ($_POST['phone'] ?? $user['phone']));
                $address = trim((string) ($_POST['shipping_address'] ?? ''));
                $city = trim((string) ($_POST['city'] ?? ''));
                $province = trim((string) ($_POST['province'] ?? ''));
                $postal = trim((string) ($_POST['postal_code'] ?? ''));
                $courier = trim((string) ($_POST['courier'] ?? 'J&T Express'));
                $shippingRates = [
                    'JNE Reguler' => 15000,
                    'JNE Express' => 22000,
                    'SiCepat REG' => 10000,
                    'Pos Indonesia' => 13000,
                    'J&T Express' => 12000,
                ];
                if (!array_key_exists($courier, $shippingRates)) {
                    throw new RuntimeException('Kurir pengiriman tidak valid.');
                }
                $paymentMethod = (string) ($_POST['payment_method'] ?? 'bank_transfer');
                $bankName = strtoupper(trim((string) ($_POST['bank_name'] ?? 'BCA')));

                if (
                    $recipient === ''
                    || $phone === ''
                    || $address === ''
                    || $city === ''
                    || $province === ''
                    || $postal === ''
                ) {
                    throw new RuntimeException('Lengkapi seluruh alamat dan informasi penerima.');
                }
                if (!in_array($paymentMethod, ['bank_transfer', 'qris', 'cod'], true)) {
                    throw new RuntimeException('Metode pembayaran tidak valid.');
                }
                if ($paymentMethod === 'bank_transfer'
                    && !in_array($bankName, ['BCA', 'BRI'], true)) {
                    throw new RuntimeException('Bank yang dipilih tidak tersedia.');
                }

                $bankLabel = $bankName === 'MANDIRI' ? 'Mandiri' : $bankName;
                $paymentType = match ($paymentMethod) {
                    'bank_transfer' => 'Transfer Bank ' . $bankLabel,
                    'qris' => 'QRIS / Dompet Digital (DANA, OVO, GoPay)',
                    default => 'Cash on Delivery (COD)',
                };

                // Terapkan perubahan jumlah barang dari ringkasan checkout secara aman.
                $checkoutQuantities = $_POST['checkout_quantities'] ?? [];
                if (is_array($checkoutQuantities)) {
                    foreach ($checkoutQuantities as $cartItemId => $requestedQuantity) {
                        $cartItemId = (int) $cartItemId;
                        $requestedQuantity = (int) $requestedQuantity;
                        if ($cartItemId <= 0) {
                            continue;
                        }
                        if ($requestedQuantity <= 0) {
                            $removeCheckoutItem = $pdo->prepare('DELETE FROM cart_items WHERE id = ? AND user_id = ?');
                            $removeCheckoutItem->execute([$cartItemId, $user['id']]);
                        } else {
                            $updateCheckoutItem = $pdo->prepare(
                                'UPDATE cart_items ci
                                 INNER JOIN books b ON b.id = ci.book_id
                                 SET ci.quantity = LEAST(?, b.stock)
                                 WHERE ci.id = ? AND ci.user_id = ? AND b.stock > 0'
                            );
                            $updateCheckoutItem->execute([$requestedQuantity, $cartItemId, $user['id']]);
                        }
                    }
                }

                $cart = get_cart_items((int) $user['id']);
                if (!$cart) {
                    throw new RuntimeException('Keranjang masih kosong.');
                }

                $subtotal = 0;
                foreach ($cart as $item) {
                    if ((int) $item['stock'] < (int) $item['quantity']) {
                        throw new RuntimeException(
                            'Stok buku "' . $item['title']
                            . '" tidak mencukupi. Perbarui keranjang.'
                        );
                    }
                    $subtotal += (float) $item['price'] * (int) $item['quantity'];
                }

                $shippingCost = $shippingRates[$courier];
                $total = $subtotal + $shippingCost;
                $orderCode = 'LUM' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
                $orderStatus = 'pending';
                $paymentStatus = $paymentMethod === 'cod' ? 'unpaid' : 'pending';

                $pdo->beginTransaction();
                try {
                    $insertOrder = $pdo->prepare(
                        'INSERT INTO orders
                         (order_code, user_id, recipient_name, email, phone, shipping_address,
                          city, province, postal_code, courier, shipping_cost, subtotal, total,
                          payment_method, payment_status, order_status, stock_released)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)'
                    );
                    $insertOrder->execute([
                        $orderCode, $user['id'], $recipient, $user['email'], $phone,
                        $address, $city, $province, $postal, $courier, $shippingCost,
                        $subtotal, $total, $paymentMethod, $paymentStatus, $orderStatus,
                    ]);
                    $orderId = (int) $pdo->lastInsertId();

                    $paymentProvider = $paymentMethod === 'cod' ? 'cod' : 'demo';
                    $transactionPrefix = $paymentMethod === 'cod' ? 'COD-' : 'DEMO-';
                    $paymentInsert = $pdo->prepare(
                        'INSERT INTO payments
                         (order_id, provider, transaction_id, payment_type, amount, status, response_json)
                         VALUES (?, ?, ?, ?, ?, "pending", ?)'
                    );
                    $paymentInsert->execute([
                        $orderId,
                        $paymentProvider,
                        $transactionPrefix . $orderCode,
                        $paymentType,
                        $total,
                        json_encode([
                            'mode' => 'simulation',
                            'message' => 'Pembayaran berhasil diproses.',
                        ], JSON_UNESCAPED_SLASHES),
                    ]);

                    foreach ($cart as $item) {
                        $lock = $pdo->prepare('SELECT stock FROM books WHERE id = ? FOR UPDATE');
                        $lock->execute([$item['book_id']]);
                        $available = (int) $lock->fetchColumn();
                        if ($available < (int) $item['quantity']) {
                            throw new RuntimeException('Stok buku berubah. Silakan ulangi checkout.');
                        }

                        $insertItem = $pdo->prepare(
                            'INSERT INTO order_items (order_id, book_id, title, author, price, quantity, subtotal)
                             VALUES (?, ?, ?, ?, ?, ?, ?)'
                        );
                        $lineTotal = (float) $item['price'] * (int) $item['quantity'];
                        $insertItem->execute([
                            $orderId, $item['book_id'], $item['title'], $item['author'],
                            $item['price'], $item['quantity'], $lineTotal,
                        ]);

                        $decrement = $pdo->prepare('UPDATE books SET stock = stock - ? WHERE id = ?');
                        $decrement->execute([$item['quantity'], $item['book_id']]);
                    }

                    $clear = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
                    $clear->execute([$user['id']]);
                    $pdo->commit();
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    throw $exception;
                }

                add_notification((int) $user['id'], 'Pesanan Berhasil Dibuat', 'Pesanan Anda ' . $orderCode . ' berhasil dibuat. Silakan lanjutkan pembayaran atau tunggu konfirmasi admin.', 'order', url('order', ['code' => $orderCode]));
                flash('success', 'Pesanan berhasil dibuat dengan nomor ' . $orderCode . '.');
                redirect(url('order', ['code' => $orderCode]));

            case 'demo_payment_result':
                $user = require_login();
                $orderCode = trim((string) ($_POST['order_code'] ?? ''));
                $result = (string) ($_POST['result'] ?? '');

                if (!in_array($result, ['paid', 'failed'], true)) {
                    throw new RuntimeException('Hasil simulasi pembayaran tidak valid.');
                }

                $pdo->beginTransaction();
                try {
                    $findOrder = $pdo->prepare(
                        'SELECT * FROM orders WHERE order_code = ? AND user_id = ? FOR UPDATE'
                    );
                    $findOrder->execute([$orderCode, $user['id']]);
                    $order = $findOrder->fetch();

                    if (!$order) {
                        throw new RuntimeException('Pesanan tidak ditemukan.');
                    }
                    if (!in_array($order['payment_method'], ['bank_transfer', 'qris'], true)
                        || $order['payment_status'] !== 'pending') {
                        throw new RuntimeException('Pesanan ini tidak sedang menunggu simulasi pembayaran.');
                    }

                    $findPayment = $pdo->prepare(
                        'SELECT * FROM payments WHERE order_id = ? FOR UPDATE'
                    );
                    $findPayment->execute([$order['id']]);
                    $payment = $findPayment->fetch();

                    if (!$payment || $payment['provider'] !== 'demo') {
                        throw new RuntimeException('Data pembayaran simulasi tidak ditemukan.');
                    }

                    if ($result === 'paid') {
                        $pdo->prepare(
                            'UPDATE orders
                             SET payment_status = "paid",
                                 order_status = "processing",
                                 updated_at = NOW()
                             WHERE id = ?'
                        )->execute([$order['id']]);
                        $pdo->prepare(
                            'UPDATE payments SET status = "paid", paid_at = NOW(), response_json = ? WHERE order_id = ?'
                        )->execute([
                            json_encode(['mode' => 'simulation', 'result' => 'paid'], JSON_UNESCAPED_SLASHES),
                            $order['id'],
                        ]);
                    } else {
                        $pdo->prepare(
                            'UPDATE orders
                             SET payment_status = "failed",
                                 order_status = "cancelled",
                                 updated_at = NOW()
                             WHERE id = ?'
                        )->execute([$order['id']]);
                        $pdo->prepare(
                            'UPDATE payments SET status = "failed", response_json = ? WHERE order_id = ?'
                        )->execute([
                            json_encode(['mode' => 'simulation', 'result' => 'failed'], JSON_UNESCAPED_SLASHES),
                            $order['id'],
                        ]);
                        release_order_stock($pdo, (int) $order['id']);
                    }

                    $pdo->commit();
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    throw $exception;
                }

                add_notification((int) $user['id'], $result === 'paid' ? 'Pembayaran Berhasil' : 'Pembayaran Gagal', $result === 'paid' ? 'Pembayaran untuk pesanan ' . $orderCode . ' berhasil diverifikasi.' : 'Pembayaran untuk pesanan ' . $orderCode . ' gagal dalam simulasi.', 'payment', url('order', ['code' => $orderCode]));
                flash(
                    $result === 'paid' ? 'success' : 'danger',
                    $result === 'paid'
                        ? 'Pembayaran berhasil. Pesanan masuk ke proses admin.'
                        : 'Pembayaran tidak berhasil. Pesanan dibatalkan dan stok dikembalikan.'
                );
                redirect(url('payment', ['code' => $orderCode]));

            case 'contact':
                $name = trim((string) ($_POST['name'] ?? ''));
                $email = strtolower(trim((string) ($_POST['email'] ?? '')));
                $subject = trim((string) ($_POST['subject'] ?? ''));
                $message = trim((string) ($_POST['message'] ?? ''));
                $whatsapp = trim((string) ($_POST['whatsapp'] ?? ''));
                if ($whatsapp !== '') {
                    $message = "WhatsApp: " . $whatsapp . "\\n\\n" . $message;
                }
                if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
                    throw new RuntimeException('Mohon lengkapi seluruh kolom pesan.');
                }
                $insert = $pdo->prepare(
                    'INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)'
                );
                $insert->execute([$name, $email, $subject, $message]);
                flash('success', 'Pesan berhasil dikirim. Tim Lumière Books akan menghubungi kamu.');
                redirect(url('contact'));

            default:
                throw new RuntimeException('Aksi tidak dikenali.');
        }
    } catch (Throwable $exception) {
        if ($isCartAjax) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store');
            echo json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }
        flash('danger', $exception->getMessage());
        $back = $_POST['_back'] ?? url('home');
        redirect((is_string($back) && str_starts_with($back, 'index.php?')) ? $back : url('home'));
    }
}
