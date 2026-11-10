<?php

declare(strict_types=1);

function handle_admin_post(): void
{
    $admin = require_admin();
    verify_csrf();
    $pdo = db();
    $action = (string) ($_POST['action'] ?? '');

    try {
        switch ($action) {
            case 'save_category':
                $id = (int) ($_POST['id'] ?? 0);
                $name = trim((string) ($_POST['name'] ?? ''));
                $description = trim((string) ($_POST['description'] ?? ''));

                if ($name === '') {
                    throw new RuntimeException('Nama kategori wajib diisi.');
                }

                $slug = strtolower(
                    trim(
                        preg_replace('/[^A-Za-z0-9-]+/', '-', $name),
                        '-'
                    )
                );

                $image = null;
                if (!empty($_FILES['image']['name'])) {
                    $file = $_FILES['image'];

                    if (
                        $file['error'] !== UPLOAD_ERR_OK ||
                        $file['size'] > 2 * 1024 * 1024
                    ) {
                        throw new RuntimeException(
                            'Upload gambar gagal atau ukuran melebihi 2 MB.'
                        );
                    }

                    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
                    $extensions = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                    ];

                    if (!isset($extensions[$mime])) {
                        throw new RuntimeException(
                            'Gambar kategori harus berupa JPG, PNG, atau WEBP.'
                        );
                    }

                    $filename = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
                    $directory = lumiere_storage_root() . '/categories';

                    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
                        throw new RuntimeException(
                            'Folder penyimpanan gambar kategori tidak dapat dibuat.'
                        );
                    }

                    $destination = $directory . '/' . $filename;
                    if (!move_uploaded_file($file['tmp_name'], $destination)) {
                        throw new RuntimeException(
                            'Tidak dapat menyimpan gambar kategori. Periksa izin folder lumiere-storage/categories.'
                        );
                    }
                    if (!is_file($destination) || filesize($destination) < 1) {
                        throw new RuntimeException(
                            'File gambar kategori tidak ditemukan setelah diunggah.'
                        );
                    }

                    $image = 'storage/categories/' . $filename;
                }

                if ($id > 0) {
                    $existing = $pdo->prepare(
                        'SELECT image FROM categories WHERE id = ?'
                    );
                    $existing->execute([$id]);
                    $old = $existing->fetch();

                    if (!$old) {
                        throw new RuntimeException('Kategori tidak ditemukan.');
                    }

                    $image = $image ?: $old['image'];

                    $statement = $pdo->prepare(
                        'UPDATE categories
                         SET name = ?, slug = ?, description = ?, image = ?
                         WHERE id = ?'
                    );
                    $statement->execute([
                        $name,
                        $slug,
                        $description,
                        $image,
                        $id,
                    ]);
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO categories (name, slug, description, image)
                         VALUES (?, ?, ?, ?)'
                    );
                    $statement->execute([
                        $name,
                        $slug,
                        $description,
                        $image,
                    ]);
                }

                flash(
                    'success',
                    $image !== null
                        ? 'Kategori dan gambar berhasil disimpan.'
                        : 'Kategori berhasil disimpan. Belum ada gambar yang dipilih.'
                );
                redirect(admin_url('categories'));

            case 'delete_category':
                $statement = $pdo->prepare('DELETE FROM categories WHERE id = ?');
                $statement->execute([(int) ($_POST['id'] ?? 0)]);
                flash('success', 'Kategori berhasil dihapus. Buku terkait tidak ikut dihapus.');
                redirect(admin_url('categories'));

            case 'save_book':
                $id = (int) ($_POST['id'] ?? 0);
                $title = trim((string) ($_POST['title'] ?? ''));
                $author = trim((string) ($_POST['author'] ?? ''));
                $categoryId = (int) ($_POST['category_id'] ?? 0);
                $price = (float) ($_POST['price'] ?? 0);
                $stock = (int) ($_POST['stock'] ?? 0);
                $description = trim((string) ($_POST['description'] ?? ''));
                $publisher = trim((string) ($_POST['publisher'] ?? ''));
                $isbn = trim((string) ($_POST['isbn'] ?? ''));

                if ($title === '' || $author === '' || $price <= 0 || $stock < 0) {
                    throw new RuntimeException('Judul, penulis, harga, dan stok harus diisi dengan benar.');
                }
                $cover = null;
                if (!empty($_FILES['cover']['name'])) {
                    $file = $_FILES['cover'];
                    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024) {
                        throw new RuntimeException('Upload cover gagal atau ukuran melebihi 2 MB.');
                    }
                    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
                    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                    if (!isset($extensions[$mime])) {
                        throw new RuntimeException('Cover harus berupa JPG, PNG, atau WEBP.');
                    }
                    $filename = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
                    $coverDirectory = lumiere_storage_root() . '/covers';
                    if (!is_dir($coverDirectory) && !mkdir($coverDirectory, 0755, true) && !is_dir($coverDirectory)) {
                        throw new RuntimeException('Folder penyimpanan cover tidak dapat dibuat.');
                    }
                    $destination = $coverDirectory . '/' . $filename;
                    if (!move_uploaded_file($file['tmp_name'], $destination)) {
                        throw new RuntimeException('Tidak dapat menyimpan file cover.');
                    }
                    $cover = 'storage/covers/' . $filename;
                }

                if ($id > 0) {
                    $existing = $pdo->prepare('SELECT cover FROM books WHERE id = ?');
                    $existing->execute([$id]);
                    $old = $existing->fetch();
                    if (!$old) {
                        throw new RuntimeException('Data buku tidak ditemukan.');
                    }
                    $cover = $cover ?: $old['cover'];
                    $statement = $pdo->prepare(
                        'UPDATE books SET category_id = ?, title = ?, author = ?, publisher = ?, isbn = ?,
                         description = ?, price = ?, stock = ?, cover = ?, updated_at = NOW()
                         WHERE id = ?'
                    );
                    $statement->execute([$categoryId ?: null, $title, $author, $publisher, $isbn,
                        $description, $price, $stock, $cover, $id]);
                } else {
                    $sku = 'LUM-' . strtoupper(bin2hex(random_bytes(3)));
                    $statement = $pdo->prepare(
                        'INSERT INTO books (category_id, sku, title, author, publisher, isbn,
                         description, price, stock, cover, status)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                    );
                    $statement->execute([$categoryId ?: null, $sku, $title, $author, $publisher,
                        $isbn, $description, $price, $stock, $cover, 'active']);
                }
                if ($id > 0) {
                    flash('success', 'Perubahan buku “' . $title . '” berhasil disimpan.');
                } else {
                    flash('success', 'Buku baru “' . $title . '” berhasil ditambahkan ke katalog.');
                }
                redirect(admin_url('books'));

            case 'delete_book':
                $statement = $pdo->prepare('DELETE FROM books WHERE id = ?');
                $statement->execute([(int) ($_POST['id'] ?? 0)]);
                flash('success', 'Buku berhasil dihapus dari katalog.');
                redirect(admin_url('books'));

            case 'toggle_book_status':
                $bookId = (int) ($_POST['book_id'] ?? 0);
                $newStatus = (string) ($_POST['new_status'] ?? '');
                if ($bookId < 1 || !in_array($newStatus, ['active', 'inactive'], true)) {
                    throw new RuntimeException('Pilihan status buku tidak valid.');
                }

                $findBook = $pdo->prepare('SELECT id, title FROM books WHERE id = ? LIMIT 1');
                $findBook->execute([$bookId]);
                $bookToUpdate = $findBook->fetch();
                if (!$bookToUpdate) {
                    throw new RuntimeException('Buku yang ingin diubah tidak ditemukan.');
                }

                $updateBookStatus = $pdo->prepare(
                    'UPDATE books SET status = ?, updated_at = NOW() WHERE id = ?'
                );
                $updateBookStatus->execute([$newStatus, $bookId]);
                if ($updateBookStatus->rowCount() < 1) {
                    $verifyStatus = $pdo->prepare('SELECT status FROM books WHERE id = ?');
                    $verifyStatus->execute([$bookId]);
                    if ($verifyStatus->fetchColumn() !== $newStatus) {
                        throw new RuntimeException('Status buku gagal diperbarui. Silakan coba lagi.');
                    }
                }

                flash('success', 'Status buku “' . $bookToUpdate['title'] . '” berhasil diubah menjadi ' . ($newStatus === 'active' ? 'Tersedia' : 'Nonaktif') . '.');
                redirect(admin_url('books'));

            case 'update_order':
                $orderId = (int) ($_POST['order_id'] ?? 0);
                $status = (string) ($_POST['order_status'] ?? 'processing');
                $paymentStatus = (string) ($_POST['payment_status'] ?? 'pending');
                $courier = trim((string) ($_POST['courier'] ?? ''));
                $tracking = trim((string) ($_POST['tracking_number'] ?? ''));
                $allowedOrder = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                $allowedPayment = ['pending', 'unpaid', 'paid', 'failed', 'expired', 'refunded'];
                if (!in_array($status, $allowedOrder, true) || !in_array($paymentStatus, $allowedPayment, true)) {
                    throw new RuntimeException('Status pesanan atau pembayaran tidak valid.');
                }

                $pdo->beginTransaction();
                $find = $pdo->prepare('SELECT * FROM orders WHERE id = ? FOR UPDATE');
                $find->execute([$orderId]);
                $order = $find->fetch();
                if (!$order) {
                    throw new RuntimeException('Pesanan tidak ditemukan.');
                }
                if ($status === 'cancelled') {
                    release_order_stock($pdo, $orderId);
                }
                $update = $pdo->prepare(
                    'UPDATE orders SET order_status = ?, payment_status = ?, courier = ?,
                     tracking_number = ?, updated_at = NOW() WHERE id = ?'
                );
                $update->execute([$status, $paymentStatus, $courier, $tracking, $orderId]);

                $paymentRecord = $pdo->prepare('SELECT id FROM payments WHERE order_id = ? LIMIT 1');
                $paymentRecord->execute([$orderId]);
                $existingPaymentId = $paymentRecord->fetchColumn();
                $normalizedPaymentStatus = $paymentStatus === 'unpaid' ? 'pending' : $paymentStatus;

                if ($existingPaymentId) {
                    $syncPayment = $pdo->prepare(
                        'UPDATE payments SET status = ?, paid_at = ? WHERE order_id = ?'
                    );
                    $syncPayment->execute([
                        $normalizedPaymentStatus,
                        $normalizedPaymentStatus === 'paid' ? date('Y-m-d H:i:s') : null,
                        $orderId,
                    ]);
                } elseif ($normalizedPaymentStatus !== 'pending') {
                    $provider = $order['payment_method'] === 'cod' ? 'cod' : 'admin_manual';
                    $paymentType = $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Verifikasi Admin';
                    $transactionId = ($order['payment_method'] === 'cod' ? 'COD-' : 'ADM-') . $order['order_code'];
                    $insertPayment = $pdo->prepare(
                        'INSERT INTO payments (order_id, provider, transaction_id, payment_type,
                         amount, status, paid_at) VALUES (?, ?, ?, ?, ?, ?, ?)'
                    );
                    $insertPayment->execute([
                        $orderId, $provider, $transactionId, $paymentType, $order['total'],
                        $normalizedPaymentStatus,
                        $normalizedPaymentStatus === 'paid' ? date('Y-m-d H:i:s') : null,
                    ]);
                }

                $pdo->commit();
                add_notification((int) $order['user_id'], 'Status Pesanan Diperbarui', 'Pesanan ' . $order['order_code'] . ' kini berstatus ' . order_status_label($status) . '.', 'shipping', url('order', ['code' => $order['order_code']]));
                flash('success', 'Status pesanan berhasil diperbarui.');
                redirect(admin_url('order', ['id' => $orderId]));

            case 'create_admin':
                $name = trim((string) ($_POST['admin_name'] ?? ''));
                $email = strtolower(trim((string) ($_POST['admin_email'] ?? '')));
                $password = (string) ($_POST['admin_password'] ?? '');
                $confirm = (string) ($_POST['admin_password_confirm'] ?? '');

                if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException('Nama dan alamat email administrator harus diisi dengan benar.');
                }
                if (strlen($password) < 10) {
                    throw new RuntimeException('Kata sandi administrator minimal 10 karakter.');
                }
                if ($password !== $confirm) {
                    throw new RuntimeException('Konfirmasi kata sandi tidak cocok.');
                }
                $exists = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $exists->execute([$email]);
                if ($exists->fetchColumn()) {
                    throw new RuntimeException('Email tersebut sudah terdaftar.');
                }

                $create = $pdo->prepare(
                    'INSERT INTO users (name, email, password_hash, role, status)
                     VALUES (?, ?, ?, "admin", "active")'
                );
                $create->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
                flash('success', 'Administrator baru berhasil dibuat. Silakan gunakan email tersebut untuk masuk.');
                redirect(admin_url('users'));

            case 'toggle_user':
                $userId = (int) ($_POST['user_id'] ?? 0);
                if ($userId === (int) $admin['id']) {
                    throw new RuntimeException('Admin tidak dapat menonaktifkan akunnya sendiri.');
                }
                $statement = $pdo->prepare(
                    'UPDATE users SET status = IF(status = "active", "inactive", "active")
                     WHERE id = ? AND role = "user"'
                );
                $statement->execute([$userId]);
                flash('success', 'Status pengguna berhasil diperbarui.');
                redirect(admin_url('users'));

            case 'update_message':
                $statement = $pdo->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
                $status = (string) ($_POST['status'] ?? 'read');
                if (!in_array($status, ['new', 'read', 'replied'], true)) {
                    $status = 'read';
                }
                $statement->execute([$status, (int) ($_POST['message_id'] ?? 0)]);
                flash('success', 'Status pesan berhasil diperbarui.');
                redirect(admin_url('messages'));

            case 'save_settings':
                $settings = [
                    'store_name' => trim((string) ($_POST['store_name'] ?? 'Lumière Books')),
                    'store_email' => trim((string) ($_POST['store_email'] ?? '')),
                    'store_phone' => trim((string) ($_POST['store_phone'] ?? '')),
                    'store_address' => trim((string) ($_POST['store_address'] ?? '')),
                    'store_description' => trim((string) ($_POST['store_description'] ?? '')),
                    'store_facebook' => trim((string) ($_POST['store_facebook'] ?? '')),
                    'store_instagram' => trim((string) ($_POST['store_instagram'] ?? '')),
                    'store_tiktok' => trim((string) ($_POST['store_tiktok'] ?? '')),
                    'store_x' => trim((string) ($_POST['store_x'] ?? '')),
                    'store_whatsapp' => trim((string) ($_POST['store_whatsapp'] ?? '')),
                    'books_per_page' => trim((string) ($_POST['books_per_page'] ?? '10')),
                    'currency' => trim((string) ($_POST['currency'] ?? 'IDR')),
                    'date_format' => trim((string) ($_POST['date_format'] ?? 'd M Y')),
                    'language' => trim((string) ($_POST['language'] ?? 'id')),
                    'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0',
                    'hours_monday' => trim((string) ($_POST['hours_monday'] ?? '08:00 - 21:00')),
                    'hours_tuesday' => trim((string) ($_POST['hours_tuesday'] ?? '08:00 - 21:00')),
                    'hours_wednesday' => trim((string) ($_POST['hours_wednesday'] ?? '08:00 - 21:00')),
                    'hours_thursday' => trim((string) ($_POST['hours_thursday'] ?? '08:00 - 21:00')),
                    'hours_friday' => trim((string) ($_POST['hours_friday'] ?? '08:00 - 21:00')),
                    'hours_saturday' => trim((string) ($_POST['hours_saturday'] ?? '08:00 - 22:00')),
                    'hours_sunday' => trim((string) ($_POST['hours_sunday'] ?? '09:00 - 21:00')),
                ];

                foreach ($settings as $key => $value) {
                    $statement = $pdo->prepare(
                        'INSERT INTO settings (setting_key, setting_value)
                         VALUES (?, ?)
                         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
                    );
                    $statement->execute([$key, $value]);
                }

                flash('success', 'Pengaturan toko berhasil disimpan.');
                redirect(admin_url('settings'));

            case 'update_admin_password':
                $currentPassword = (string) ($_POST['current_password'] ?? '');
                $newPassword = (string) ($_POST['new_password'] ?? '');
                $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

                $adminQuery = $pdo->prepare(
                    'SELECT password_hash FROM users WHERE id = ? LIMIT 1'
                );
                $adminQuery->execute([$admin['id']]);
                $adminRecord = $adminQuery->fetch();

                if (
                    !$adminRecord ||
                    !password_verify($currentPassword, $adminRecord['password_hash'])
                ) {
                    throw new RuntimeException('Password lama tidak sesuai.');
                }

                if (strlen($newPassword) < 8) {
                    throw new RuntimeException(
                        'Password baru minimal terdiri dari 8 karakter.'
                    );
                }

                if ($newPassword !== $confirmPassword) {
                    throw new RuntimeException(
                        'Konfirmasi password baru belum sama.'
                    );
                }

                $updatePassword = $pdo->prepare(
                    'UPDATE users SET password_hash = ? WHERE id = ?'
                );
                $updatePassword->execute([
                    password_hash($newPassword, PASSWORD_DEFAULT),
                    $admin['id'],
                ]);

                flash('success', 'Password admin berhasil diperbarui.');
                redirect(admin_url('settings'));

            default:
                throw new RuntimeException('Aksi admin tidak dikenali.');
        }
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        flash('danger', $exception->getMessage());
        $back = $_POST['_back'] ?? admin_url();
        redirect((is_string($back) && str_starts_with($back, 'admin.php?')) ? $back : admin_url());
    }
}
