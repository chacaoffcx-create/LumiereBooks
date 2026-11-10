<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$envFile = $root . '/.env';

if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim(trim($value), "\"'");

        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

return [
    'app_env' => getenv('APP_ENV') ?: 'local',
    'app_url' => rtrim(getenv('APP_URL') ?: '', '/'),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Jakarta',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'lumiere_books',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
    ],
];
