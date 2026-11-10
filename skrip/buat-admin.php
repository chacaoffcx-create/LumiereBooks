<?php

declare(strict_types=1);

require dirname(__DIR__) . '/sistem/mulai.php';

if (PHP_SAPI !== 'cli') {
    exit("Script ini hanya dapat dijalankan melalui terminal.\n");
}

$email = $argv[1] ?? '';
$password = $argv[2] ?? '';
$name = $argv[3] ?? 'Administrator Lumière Books';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 10) {
    exit("Penggunaan: php skrip/buat-admin.php admin@email.com 'PasswordMinimal10' 'Nama Admin'\n");
}

$statement = db()->prepare(
    'INSERT INTO users (name, email, password_hash, role, status)
     VALUES (?, ?, ?, "admin", "active")
     ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        password_hash = VALUES(password_hash),
        role = "admin",
        status = "active"'
);
$statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
echo "Admin berhasil dibuat/diperbarui: {$email}\n";
