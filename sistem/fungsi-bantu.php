<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money(int|float|string $amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function date_id(?string $date, bool $withTime = false): string
{
    if (!$date) {
        return '-';
    }

    $format = $withTime ? 'd M Y, H:i' : 'd M Y';
    return date($format, strtotime($date));
}

function url(string $page = 'home', array $params = []): string
{
    return 'index.php?' . http_build_query(array_merge(['page' => $page], $params));
}

function admin_url(string $page = 'dashboard', array $params = []): string
{
    return 'admin.php?' . http_build_query(array_merge(['page' => $page], $params));
}

function asset(string $path): string
{
    return 'assets/' . ltrim($path, '/');
}

/** Persistent uploads live beside the project folder, so replacing a project ZIP
 * never overwrites customer/admin uploaded images. This path is intended for the
 * standard XAMPP layout: htdocs/lumiere-books and htdocs/lumiere-storage.
 */
function lumiere_storage_root(): string
{
    return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'lumiere-storage';
}

function lumiere_storage_url(string $relativePath): string
{
    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
    $parts = array_map('rawurlencode', explode('/', $relativePath));
    // Public pages are served from /lumiere-books/public/, so two levels up
    // reaches htdocs and then the sibling lumiere-storage directory.
    return '../../lumiere-storage/' . implode('/', $parts);
}

function lumiere_storage_file(string $relativePath): string
{
    $relativePath = ltrim(str_replace(['\\', '..'], ['/', ''], $relativePath), '/');
    return lumiere_storage_root() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
}

function avatar_url(?string $avatar): string
{
    $avatar = trim((string) $avatar);
    if ($avatar === '') {
        return '';
    }
    if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
        return $avatar;
    }
    if (str_starts_with($avatar, 'storage/')) {
        $relative = substr($avatar, strlen('storage/'));
        if (is_file(lumiere_storage_file($relative))) {
            return lumiere_storage_url($relative);
        }
    }
    // Backward compatibility for avatars uploaded by older project versions.
    return asset($avatar);
}

function redirect(string $target): never
{
    header('Location: ' . $target);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $submitted = $_POST['_csrf'] ?? '';
    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(419);
        exit('Sesi formulir kedaluwarsa. Silakan kembali dan muat ulang halaman.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $messages;
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $loaded = false;
    static $user = null;

    if (!$loaded) {
        $statement = db()->prepare(
            'SELECT id, name, email, phone, birth_date, gender, avatar, role, status, created_at
             FROM users WHERE id = ? LIMIT 1'
        );
        $statement->execute([$_SESSION['user_id']]);
        $user = $statement->fetch() ?: null;
        $loaded = true;

        if (!$user || $user['status'] !== 'active') {
            unset($_SESSION['user_id']);
            $user = null;
        }
    }

    return $user;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        flash('warning', 'Silakan login terlebih dahulu.');
        redirect(url('login'));
    }
    return $user;
}

function require_admin(): array
{
    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        flash('warning', 'Halaman ini hanya dapat diakses oleh admin.');
        redirect(url('login'));
    }
    return $user;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
}

function cart_count(): int
{
    $user = current_user();
    if (!$user) {
        return 0;
    }

    $statement = db()->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = ?');
    $statement->execute([$user['id']]);
    return (int) $statement->fetchColumn();
}

function get_cart_items(int $userId): array
{
    $statement = db()->prepare(
        'SELECT ci.id AS cart_id, ci.quantity, b.id AS book_id, b.title,
                b.author, b.price, b.stock, b.cover, b.status
         FROM cart_items ci
         INNER JOIN books b ON b.id = ci.book_id
         WHERE ci.user_id = ? AND b.status = "active"
         ORDER BY ci.created_at DESC'
    );
    $statement->execute([$userId]);
    return $statement->fetchAll();
}

function order_for_user(string $code, int $userId): ?array
{
    $statement = db()->prepare('SELECT * FROM orders WHERE order_code = ? AND user_id = ? LIMIT 1');
    $statement->execute([$code, $userId]);
    return $statement->fetch() ?: null;
}

function order_items(int $orderId): array
{
    $statement = db()->prepare('SELECT oi.*, b.cover FROM order_items oi LEFT JOIN books b ON b.id = oi.book_id WHERE oi.order_id = ? ORDER BY oi.id');
    $statement->execute([$orderId]);
    return $statement->fetchAll();
}

function order_status_label(string $status): string
{
    return [
        'pending' => 'Menunggu Pembayaran',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'delivered' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ][$status] ?? ucfirst($status);
}

function payment_status_label(string $status): string
{
    return [
        'pending' => 'Menunggu Pembayaran',
        'unpaid' => 'Belum Dibayar',
        'paid' => 'Berhasil',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        'refunded' => 'Dikembalikan',
    ][$status] ?? ucfirst($status);
}

function payment_method_label(string $method, ?string $paymentType = null): string
{
    if ($paymentType !== null && $paymentType !== '') {
        return $paymentType;
    }

    return match ($method) {
        'bank_transfer' => 'Transfer Bank (Simulasi)',
        'qris' => 'QRIS (Simulasi)',
        'cod' => 'Cash on Delivery (COD)',
        default => 'Belum ditentukan',
    };
}

function status_class(string $status): string
{
    return match ($status) {
        'paid', 'delivered', 'active', 'shipped' => 'success',
        'failed', 'expired', 'cancelled', 'inactive' => 'danger',
        'pending', 'unpaid', 'processing' => 'warning',
        default => 'info',
    };
}

function app_setting(string $key, string $fallback = ''): string
{
    $statement = db()->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
    $statement->execute([$key]);
    $value = $statement->fetchColumn();
    return $value === false ? $fallback : (string) $value;
}

function cover_url(?string $cover): string
{
    if (!$cover) {
        return asset('covers/default.png');
    }

    if (str_starts_with($cover, 'http://') || str_starts_with($cover, 'https://')) {
        return $cover;
    }

    // New uploads are stored outside the replaceable project directory.
    if (str_starts_with($cover, 'storage/')) {
        $relative = substr($cover, strlen('storage/'));
        if (is_file(lumiere_storage_file($relative))) {
            return lumiere_storage_url($relative);
        }
    }

    // Backward compatibility for uploads created by earlier ZIP versions.
    $relative = ltrim(str_replace('\\', '/', $cover), '/');
    $publicFile = dirname(__DIR__) . '/public/' . $relative;
    if (is_file($publicFile)) {
        return $relative;
    }

    return asset('covers/default.png');
}

function lumiere_category_photo_url(array $category): string
{
    // If an admin uploaded a custom category image, always show that image.
    if (!empty($category['image'])) {
        return category_image_url($category);
    }

    $slug = trim((string) ($category['slug'] ?? ''));
    $photos = [
        'fiksi', 'non-fiksi', 'self-help', 'bisnis-ekonomi', 'pendidikan',
        'anak-anak', 'remaja', 'horor', 'romance', 'klasik',
    ];

    if (in_array($slug, $photos, true)) {
        return asset('images/categories/' . $slug . '.webp');
    }

    return category_image_url($category);
}

function category_image_url(array $category): string
{
    $image = trim((string) ($category['image'] ?? ''));

    if ($image !== '') {
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        if (str_starts_with($image, 'storage/')) {
            $storageRelative = substr($image, strlen('storage/'));
            if (is_file(lumiere_storage_file($storageRelative))) {
                return lumiere_storage_url($storageRelative);
            }
        }

        $relative = ltrim(str_replace('\\', '/', $image), '/');
        $publicFile = dirname(__DIR__) . '/public/' . $relative;
        if (is_file($publicFile)) {
            return $relative;
        }
    }

    $slug = trim((string) ($category['slug'] ?? ''));
    $slug = $slug !== '' ? $slug : 'fiksi';
    $webp = dirname(__DIR__) . '/public/assets/images/categories/' . $slug . '.webp';
    if (is_file($webp)) {
        return asset('images/categories/' . $slug . '.webp');
    }

    return asset('categories/' . $slug . '.png');
}

function date_long_id(?string $date = null): string
{
    $timestamp = $date ? strtotime($date) : time();
    $days = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
    ];
    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    return $days[date('l', $timestamp)] . ', ' .
        date('d', $timestamp) . ' ' .
        $months[(int) date('n', $timestamp)] . ' ' .
        date('Y', $timestamp);
}

function ui_icon(string $name, string $class = ''): string
{
    $icons = [
        'home' => '<path d="m3 10 9-7 9 7"/><path d="M5 9v11h14V9"/><path d="M9 20v-6h6v6"/>',
        'book' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
        'book-open' => '<path d="M12 7v14"/><path d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13"/><path d="M3 18a3 3 0 0 1 3-3h2a4 4 0 0 1 4 4 4 4 0 0 1 4-4h2a3 3 0 0 1 3 3"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'lock' => '<rect x="3" y="10" width="18" height="11" rx="2"/><path d="M7 10V7a5 5 0 0 1 10 0v3"/>',
        'camera' => '<path d="M14 5h-4l-2 3H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-3Z"/><circle cx="12" cy="14" r="3"/>',
        'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8M7 3v5h8"/>',
        'log-out' => '<path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>',
        'help-circle' => '<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01"/>' ,
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'cart' => '<circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 3h2l2.4 12.1a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L22 7H5"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="m7 14 4-4 4 3 6-7"/><path d="M17 6h4v4"/>',
        'settings' => '<path d="M12 2.8c1.3 0 2 1.2 2.8 2.2 1 .4 2.4 0 3.4 0.8 1 .9.4 2.3.5 3.4.7 1 2.1 1.6 2.1 2.8s-1.4 1.8-2.1 2.8c-.1 1.1.5 2.5-.5 3.4-1 .8-2.4.4-3.4.8-.8 1-1.5 2.2-2.8 2.2s-2-1.2-2.8-2.2c-1-.4-2.4 0-3.4-.8-1-.9-.4-2.3-.5-3.4C4.6 13.8 3.2 13.2 3.2 12s1.4-1.8 2.1-2.8c.1-1.1-.5-2.5.5-3.4 1-.8 2.4-.4 3.4-.8.8-1 1.5-2.2 2.8-2.2Z"/><circle cx="12" cy="12" r="2.5"/>',
        'gear' => '<path d="M12 2.8c1.3 0 2 1.2 2.8 2.2 1 .4 2.4 0 3.4 0.8 1 .9.4 2.3.5 3.4.7 1 2.1 1.6 2.1 2.8s-1.4 1.8-2.1 2.8c-.1 1.1.5 2.5-.5 3.4-1 .8-2.4.4-3.4.8-.8 1-1.5 2.2-2.8 2.2s-2-1.2-2.8-2.2c-1-.4-2.4 0-3.4-.8-1-.9-.4-2.3-.5-3.4C4.6 13.8 3.2 13.2 3.2 12s1.4-1.8 2.1-2.8c.1-1.1-.5-2.5.5-3.4 1-.8 2.4-.4 3.4-.8.8-1 1.5-2.2 2.8-2.2Z"/><circle cx="12" cy="12" r="2.5"/>',
        'external-link' => '<path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'edit' => '<path d="m16 4 4 4L8 20H4v-4Z"/><path d="m14 6 4 4"/>',
        'trash' => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/>',
        'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'filter' => '<path d="M4 5h16l-6.5 7.5V19l-3 1v-7.5Z"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'truck' => '<path d="M3 6h11v11H3z"/><path d="M14 10h4l3 3v4h-7z"/><circle cx="7.5" cy="19" r="1.5"/><circle cx="17.5" cy="19" r="1.5"/>',
        'shield' => '<path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/>',
        'wallet' => '<rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 8h18M16 14h2"/>',
        'box' => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M3 8v9l9 5 9-5V8M12 13v9"/>',
        'tag' => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8Z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7l.5 2.8a2 2 0 0 1-.6 1.8L7.7 9.6a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 1.8-.6l2.8.5a2 2 0 0 1 1.7 2.7Z"/>',
        'map-pin' => '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'message-circle' => '<path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z"/><path d="M8 12h.01M12 12h.01M16 12h.01"/>',
        'send' => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>',
        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h8"/>',
        'headset' => '<path d="M3 13v-1a9 9 0 0 1 18 0v1"/><path d="M5 13h2v6H5a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2ZM19 13h-2v6h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2Z"/>',
        'leaf' => '<path d="M20 4c-8 0-14 3-14 10a6 6 0 0 0 6 6c7 0 10-6 8-16Z"/><path d="M4 20c3-5 7-8 13-11"/>',
        'star' => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9Z"/>',
        'heart' => '<path d="M20.8 8.8c0 5.2-8.8 11-8.8 11s-8.8-5.8-8.8-11A4.8 4.8 0 0 1 12 6a4.8 4.8 0 0 1 8.8 2.8Z"/>',
        'gift' => '<rect x="3" y="8" width="18" height="13" rx="2"/><path d="M12 8v13M2 12h20M12 8H7.5a2.5 2.5 0 1 1 2.5-2.5V8Zm0 0h4.5A2.5 2.5 0 1 0 14 5.5V8Z"/>',
        'shield-check' => '<path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/>',
        'refresh-cw' => '<path d="M20 7v5h-5M4 17v-5h5"/><path d="M5.6 9A7 7 0 0 1 18 6l2 6M4 12l2 6a7 7 0 0 0 12.4-3"/>',
        'image' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m21 15-5-5L5 20"/>',
        'money' => '<circle cx="12" cy="12" r="9"/><path d="M15 8.5c-.6-.8-1.6-1.2-3-1.2-1.7 0-2.8.8-2.8 2 0 3.5 6.3 1.2 6.3 4.8 0 1.3-1.2 2.2-3.2 2.2-1.4 0-2.6-.5-3.3-1.4M12 5.5v13"/>',
        'graduation' => '<path d="m2 10 10-6 10 6-10 6-10-6Z"/><path d="M6 12.5V17c3.5 2.5 8.5 2.5 12 0v-4.5M22 10v6"/>',
        'ghost' => '<path d="M5 20v-9a7 7 0 0 1 14 0v9l-3-2-4 2-4-2-3 2Z"/><circle cx="9" cy="11" r="1"/><circle cx="15" cy="11" r="1"/>',
        'kids' => '<circle cx="12" cy="12" r="9"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/>',
        'classical' => '<path d="M4 21h16M6 18V8M12 18V8M18 18V8M4 8h16l-8-5-8 5Z"/>',
        'sprout' => '<path d="M12 21v-9"/><path d="M12 12C4 12 3 7 4 4c5 0 8 2 8 8ZM12 15c0-6 4-9 8-9 1 5-1 9-8 9Z"/>',
        'briefcase-business' => '<rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v2h4v-2"/>',
        'circle-check' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>',
        'circle-x' => '<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/>',
        'clipboard-list' => '<rect x="5" y="4" width="14" height="18" rx="2"/><path d="M9 4.5V3h6v1.5M9 10h6M9 14h6M9 18h3"/>',
        'package-open' => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M3 8v9l9 5 9-5V8M12 13v9M7 5.8l10 5.5"/>',
        'credit-card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
        'file-text' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h8"/>',
    ];

    $paths = $icons[$name] ?? $icons['book'];
    $className = trim('ui-icon ' . $class);

    return '<svg class="' . e($className) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' .
        $paths .
        '</svg>';
}


function add_notification(int $userId, string $title, string $message, string $type = 'system', ?string $targetUrl = null): void
{
    $statement = db()->prepare('INSERT INTO notifications (user_id, title, message, type, target_url) VALUES (?, ?, ?, ?, ?)');
    $statement->execute([$userId, $title, $message, $type, $targetUrl]);
}
