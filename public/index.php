<?php

declare(strict_types=1);

require dirname(__DIR__) . '/sistem/mulai.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedAction = (string) ($_POST['action'] ?? '');
    if (in_array($postedAction, ['upload_avatar','favorite_toggle','favorites_delete_all','save_profile','change_password','save_address','delete_address','default_address','notification_read','notifications_read_all','notifications_delete_all'], true)) {
        handle_account_post();
    }
    handle_store_post();
}

$user = current_user();

// Ambil daftar favorit pengguna agar ikon hati selalu mencerminkan status tersimpan
// pada beranda, katalog, dan halaman detail buku.
$favoriteBookIds = [];
if ($user) {
    $favoriteQuery = db()->prepare('SELECT book_id FROM favorites WHERE user_id = ?');
    $favoriteQuery->execute([(int) $user['id']]);
    $favoriteBookIds = array_map('intval', $favoriteQuery->fetchAll(PDO::FETCH_COLUMN));
}

// PETA HALAMAN: alamat web (?page=...) -> nama file di tampilan/pelanggan/halaman/
// Mau ubah isi sebuah halaman? Cari nama file-nya di daftar ini.
$fileHalaman = [
    'welcome' => 'selamat-datang',
    'home' => 'beranda',
    'books' => 'daftar-buku',
    'book' => 'detail-buku',
    'about' => 'tentang-kami',
    'contact' => 'kontak',
    'login' => 'masuk',
    'register' => 'daftar',
    'cart' => 'keranjang',
    'checkout' => 'checkout',
    'payment' => 'pembayaran',
    'order' => 'detail-pesanan',
    'my-orders' => 'pesanan-saya',
    'categories' => 'kategori',
    'profile' => 'profil',
    'favorites' => 'favorit',
    'addresses' => 'alamat',
    'notifications' => 'notifikasi',
    'help' => 'bantuan',
    'not-found' => 'tidak-ditemukan',
];

$page = (string) ($_GET['page'] ?? ($user ? 'home' : 'welcome'));
$allowedPages = [
    'welcome', 'home', 'books', 'book', 'about', 'contact', 'login', 'register',
    'cart', 'checkout', 'payment', 'order', 'my-orders', 'categories', 'profile', 'favorites', 'addresses', 'notifications', 'help',
];
if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    $page = 'not-found';
}

if ($page === 'login' && !$user && isset($_GET['from']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $loginReason = (string) $_GET['from'];
    if ($loginReason === 'keranjang') {
        flash('warning', 'Masuk dulu ya untuk memasukkan buku ke keranjang.');
    } elseif ($loginReason === 'favorit') {
        flash('warning', 'Masuk dulu ya untuk menyimpan buku ke favorit.');
    }
}

if ($page === 'welcome' && $user) {
    redirect(url('home'));
}
if ($page === 'home' && !$user) {
    redirect(url('welcome'));
}

$categories = db()->query(
    "SELECT * FROM categories
     ORDER BY FIELD(
        slug,
        'fiksi', 'non-fiksi', 'self-help', 'bisnis-ekonomi',
        'pendidikan', 'anak-anak', 'remaja', 'horor',
        'romance', 'klasik', 'sejarah', 'thriller'
     ), name"
)->fetchAll();

if ($page === 'cart' || $page === 'checkout') {
    $user = require_login();
    if ($page === 'checkout') {
        $savedAddressQuery = db()->prepare('SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC');
        $savedAddressQuery->execute([$user['id']]);
        $savedAddresses = $savedAddressQuery->fetchAll();
    }
    $cartItems = get_cart_items((int) $user['id']);
    $cartSubtotal = array_sum(array_map(
        static fn (array $item): float => (float) $item['price'] * (int) $item['quantity'],
        $cartItems
    ));
}

if ($page === 'profile' || $page === 'addresses') {
    $user = require_login();
    $addressStatement = db()->prepare('SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC');
    $addressStatement->execute([$user['id']]);
    $addresses = $addressStatement->fetchAll();
    $editAddress = null;
    if ($page === 'addresses' && !empty($_GET['edit'])) {
        $editQuery = db()->prepare('SELECT * FROM addresses WHERE id = ? AND user_id = ?');
        $editQuery->execute([(int) $_GET['edit'], $user['id']]);
        $editAddress = $editQuery->fetch() ?: null;
    }
}

if ($page === 'favorites') {
    $user = require_login();
    $favoriteStatement = db()->prepare('SELECT b.*, c.name AS category_name FROM favorites f JOIN books b ON b.id=f.book_id LEFT JOIN categories c ON c.id=b.category_id WHERE f.user_id=? ORDER BY f.created_at DESC');
    $favoriteStatement->execute([$user['id']]);
    $favoriteBooks = $favoriteStatement->fetchAll();
}

if ($page === 'notifications') {
    $user = require_login();
    $notificationStatement = db()->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 50');
    $notificationStatement->execute([$user['id']]);
    $notifications = $notificationStatement->fetchAll();
}

if ($page === 'my-orders') {
    $user = require_login();
    $statement = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $statement->execute([$user['id']]);
    $orders = $statement->fetchAll();
    foreach ($orders as &$orderRow) {
        $itemStatement = db()->prepare('SELECT oi.*, b.cover FROM order_items oi LEFT JOIN books b ON b.id = oi.book_id WHERE oi.order_id = ? ORDER BY oi.id');
        $itemStatement->execute([(int) $orderRow['id']]);
        $orderRow['items'] = $itemStatement->fetchAll();
    }
    unset($orderRow);

    $orderFilter = trim((string) ($_GET['status'] ?? 'all'));
    $orderSearch = trim((string) ($_GET['q'] ?? ''));
    $orderSort = (string) ($_GET['sort'] ?? 'newest');
    $validOrderFilters = ['all', 'pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($orderFilter, $validOrderFilters, true)) $orderFilter = 'all';
    if (!in_array($orderSort, ['newest', 'oldest', 'highest', 'lowest'], true)) $orderSort = 'newest';

    $orderCounts = array_fill_keys($validOrderFilters, 0);
    foreach ($orders as $orderRow) {
        $orderCounts['all']++;
        if (isset($orderCounts[$orderRow['order_status']])) $orderCounts[$orderRow['order_status']]++;
    }
    $orders = array_values(array_filter($orders, static function ($orderRow) use ($orderFilter, $orderSearch) {
        if ($orderFilter !== 'all' && $orderRow['order_status'] !== $orderFilter) return false;
        if ($orderSearch !== '') {
            $haystack = strtolower((string) $orderRow['order_code']);
            foreach ($orderRow['items'] as $itemRow) $haystack .= ' ' . strtolower((string) $itemRow['title']);
            if (strpos($haystack, strtolower($orderSearch)) === false) return false;
        }
        return true;
    }));
    usort($orders, static function ($a, $b) use ($orderSort) {
        return match ($orderSort) {
            'oldest' => strcmp((string) $a['created_at'], (string) $b['created_at']),
            'highest' => (float) $b['total'] <=> (float) $a['total'],
            'lowest' => (float) $a['total'] <=> (float) $b['total'],
            default => strcmp((string) $b['created_at'], (string) $a['created_at']),
        };
    });
}

if ($page === 'order' || $page === 'payment') {
    $user = require_login();
    $code = trim((string) ($_GET['code'] ?? ''));
    $order = order_for_user($code, (int) $user['id']);
    if (!$order && $user['role'] === 'admin' && $page === 'order') {
        $adminOrder = db()->prepare('SELECT * FROM orders WHERE order_code = ? LIMIT 1');
        $adminOrder->execute([$code]);
        $order = $adminOrder->fetch() ?: null;
    }
    if (!$order) {
        flash('danger', 'Pesanan tidak ditemukan.');
        redirect(url('my-orders'));
    }
    $items = order_items((int) $order['id']);
    $payment = get_payment_for_order((int) $order['id']);
}

if ($page === 'home' || $page === 'welcome') {
    $featuredBooks = db()->query(
        'SELECT b.*, c.name AS category_name FROM books b
         LEFT JOIN categories c ON c.id = b.category_id
         WHERE b.status = "active" ORDER BY b.is_featured DESC, b.id ASC LIMIT 8'
    )->fetchAll();
}

include __DIR__ . '/tampilan/pelanggan/bagian/kepala-halaman.php';

$viewFile = __DIR__ . '/tampilan/pelanggan/halaman/' . ($fileHalaman[$page] ?? $page) . '.php';
if (is_file($viewFile)) {
    include $viewFile;
} else {
    include __DIR__ . '/tampilan/pelanggan/halaman/tidak-ditemukan.php';
}

include __DIR__ . '/tampilan/pelanggan/bagian/kaki-halaman.php';
