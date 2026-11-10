<?php

declare(strict_types=1);

$config = require dirname(__DIR__) . '/pengaturan/konfigurasi.php';
date_default_timezone_set($config['timezone']);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/koneksi-database.php';
require_once __DIR__ . '/fungsi-bantu.php';
require_once __DIR__ . '/pembayaran.php';
require_once __DIR__ . '/pengendali/toko.php';
require_once __DIR__ . '/pengendali/admin.php';

require_once __DIR__ . '/pengendali/akun.php';
