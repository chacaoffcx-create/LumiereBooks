<?php

$flashes = get_flashes();


$adminDisplayName = trim((string) ($admin['name'] ?? ''));

if ($adminDisplayName === '') {
    $adminDisplayName = 'Admin';

}

$pageTitles = [
    'dashboard' => 'Selamat Datang, ' . $adminDisplayName . '!',
    
    'books' => 'Kelola Buku',
    
    'categories' => 'Kelola Kategori Buku',
    
    'users' => 'Data User',
    
    'orders' => 'Pesanan Buku',
    
    'payments' => 'Kelola Pembayaran',
    
    'order' => 'Detail Pesanan',
    
    'reports' => 'Laporan Pendapatan',
    
    'messages' => 'Pesan Masuk',
    
    'settings' => 'Pengaturan Admin',
    
];


$pageDescriptions = [
    'dashboard' => 'Kelola data buku, user, pesanan, dan pantau perkembangan Lumière Books.',
    
    'books' => 'Kelola koleksi buku yang tersedia di Lumière Books.',
    
    'categories' => 'Tambah, ubah, atau hapus kategori buku sesuai kebutuhan.',
    
    'users' => 'Daftar pengguna yang sudah terdaftar di Lumière Books.',
    
    'orders' => 'Kelola semua pesanan buku dari pelanggan Lumière Books.',
    
    'payments' => 'Pantau dan verifikasi transaksi pelanggan.',
    
    'order' => 'Periksa informasi pesanan dan perbarui proses pengiriman.',
    
    'reports' => 'Pantau perkembangan pendapatan toko buku Lumière Books.',
    
    'messages' => 'Kelola pertanyaan dan pesan dari pelanggan.',
    
    'settings' => 'Kelola pengaturan sistem, informasi toko, dan akun admin.',
    
];


$unreadMessages = (int) db()->query(
    'SELECT COUNT(*) FROM contact_messages WHERE status = "new"'
)->fetchColumn();


$activeNavPage = $page === 'order' ? 'orders' : $page;

?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#fbf0e7">
        <link rel="icon" type="image/png" href="<?= e(asset('logo-lumiere.png')) ?>">
        <title>
            Admin
            <?= e(ucfirst($page)) ?>
            | Lumière Books
        </title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        >
        <link
            href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&display=swap"
            rel="stylesheet"
        >
        <link rel="stylesheet" href="<?= e(asset('css/umum/1-dasar.css')) ?>">
        <link rel="stylesheet" href="<?= e(asset('css/umum/2-desain-baru.css')) ?>">
        <link rel="stylesheet" href="<?= e(asset('css/umum/3-tema-lumiere.css')) ?>">
        <link rel="stylesheet" href="<?= e(asset('css/admin/1-admin-premium.css')) ?>">
        <link rel="stylesheet" href="<?= e(asset('css/admin/2-admin-hangat.css')) ?>?v=12.8">
    </head>
    <body class="admin-body redesign-admin">
        <div class="admin-shell">
            <aside class="admin-sidebar" data-admin-sidebar>
                <a class="admin-brand" href="<?= e(admin_url()) ?>">
                    <span class="brand-mark">
                        <img src="<?= e(asset('logo-lumiere.png')) ?>" alt="Lumière Books">
                    </span>
                    <span class="admin-brand-copy">
                        <b>
                            Lumière Books
                        </b>
                        <small>
                            Admin Panel
                        </small>
                    </span>
                </a>
                <nav class="admin-nav" aria-label="Navigasi admin">
                    <a
                        class="<?= $activeNavPage === 'dashboard' ? 'active' : '' ?>"
                        href="<?= e(admin_url()) ?>"
                    >
                        <?= ui_icon('home') ?>
                        <span>
                            Beranda
                        </span>
                    </a>
                    <a
                        class="<?= $activeNavPage === 'books' ? 'active' : '' ?>"
                        href="<?= e(admin_url('books')) ?>"
                    >
                        <?= ui_icon('book-open') ?>
                        <span>
                            Kelola Buku
                        </span>
                    </a>
                    <a
                        class="<?= $activeNavPage === 'categories' ? 'active' : '' ?>"
                        href="<?= e(admin_url('categories')) ?>"
                    >
                        <?= ui_icon('grid') ?>
                        <span>
                            Kategori
                        </span>
                    </a>
                    <a
                        class="<?= $activeNavPage === 'users' ? 'active' : '' ?>"
                        href="<?= e(admin_url('users')) ?>"
                    >
                        <?= ui_icon('user') ?>
                        <span>
                            Pelanggan
                        </span>
                    </a>
                    <a
                        class="<?= $activeNavPage === 'orders' ? 'active' : '' ?>"
                        href="<?= e(admin_url('orders')) ?>"
                    >
                        <?= ui_icon('cart') ?>
                        <span>
                            Pesanan
                        </span>
                    </a>
                    <a
                        class="<?= $activeNavPage === 'payments' ? 'active' : '' ?>"
                        href="<?= e(admin_url('payments')) ?>"
                    >
                        <?= ui_icon('wallet') ?>
                        <span>
                            Pembayaran
                        </span>
                    </a>
                    <a
                        class="<?= $activeNavPage === 'messages' ? 'active' : '' ?>"
                        href="<?= e(admin_url('messages')) ?>"
                    >
                        <?= ui_icon('mail') ?>
                        <span>
                            Pesan Masuk
                        </span>
                    </a>
                    <a
                        class="<?= $activeNavPage === 'reports' ? 'active' : '' ?>"
                        href="<?= e(admin_url('reports')) ?>"
                    >
                        <?= ui_icon('chart') ?>
                        <span>
                            Laporan
                        </span>
                    </a>
                    <a
                        class="<?= $activeNavPage === 'settings' ? 'active' : '' ?>"
                        href="<?= e(admin_url('settings')) ?>"
                    >
                        <?= ui_icon('gear') ?>
                        <span>
                            Pengaturan
                        </span>
                    </a>
                    <a
                        class="admin-nav-website"
                        href="<?= e(url('home')) ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        <?= ui_icon('external-link') ?>
                        <span>
                            Lihat Website
                        </span>
                    </a>
                </nav>
                <div class="admin-sidebar-art" aria-hidden="true">
                    <p>
                        Setiap Buku,
                        <br>
                        Sejuta Cahaya.
                    </p>
                    <small>
                        — Lumière Books
                    </small>
                </div>
            </aside>
            <div class="admin-main">
                <header class="admin-topbar">
                    <button
                        type="button"
                        class="admin-menu-toggle"
                        data-admin-toggle
                        aria-label="Buka navigasi"
                    >
                        ☰
                    </button>
                    <form
                        action="<?= e(admin_url('books')) ?>"
                        method="get"
                        class="admin-search"
                    >
                        <input type="hidden" name="page" value="books">
                        <?= ui_icon('search') ?>
                        <input
                            type="search"
                            name="q"
                            placeholder="Cari data buku, pesanan, atau pelanggan..."
                            aria-label="Cari data admin"
                        >
                    </form>
                    <div class="admin-top-actions">
                        <a
                            class="admin-notification"
                            href="<?= e(admin_url('messages')) ?>"
                            title="Pesan pelanggan"
                        >
                            <?= ui_icon('bell') ?>
                            <?php if ($unreadMessages > 0): ?>
                                <span>
                                <?= min($unreadMessages, 99) ?>
                                </span>
                                <?php endif; ?>
                            </a>
                            <div class="admin-profile">
                                <span class="admin-avatar">
                                <?= e(strtoupper(substr($admin['name'], 0, 1))) ?>
                                </span>
                                <span class="admin-profile-copy">
                                <b>
                                <?= e($admin['name']) ?>
                                </b>
                                <small>
                                Administrator
                                </small>
                                </span>
                                <details class="admin-profile-menu">
                                <summary aria-label="Menu akun">
                                ⌄
                                </summary>
                                <div>
                                <a href="<?= e(admin_url('settings')) ?>">
                                Pengaturan Akun
                                </a>
                                <form method="post" action="<?= e(url('home')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="logout">
                                <button type="submit">
                                Keluar
                                </button>
                                </form>
                                </div>
                                </details>
                            </div>
                        </div>
                    </header>
                    <main class="admin-content">
                        <div class="admin-page-heading">
                            <div>
                                <h1>
                                <?= e($pageTitles[$page] ?? 'Admin Panel') ?>
                                </h1>
                                <p>
                                <?= e($pageDescriptions[$page] ?? '') ?>
                                </p>
                            </div>
                            <div class="admin-date">
                                <?= ui_icon('calendar') ?>
                                <span>
                                <b>
                                <?= e(date_long_id()) ?>
                                </b>
                                <small>
                                <?= date('H:i') ?>
                                WIB
                                </small>
                                </span>
                            </div>
                        </div>
                        <?php foreach ($flashes as $flash): ?>
                            <div
                                class="alert alert-<?= e($flash['type']) ?>"
                                data-alert
                            >
                                <span>
                                <?= e($flash['message']) ?>
                                </span>
                                <button type="button" data-alert-close>
                            ×
                                </button>
                            </div>
                            <?php endforeach; ?>
