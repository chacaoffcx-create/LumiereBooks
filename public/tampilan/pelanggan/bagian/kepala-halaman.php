<?php
$user = current_user();
$flashes = get_flashes();
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#fbf0e7">
        <link rel="icon" type="image/png" href="<?= e(asset('logo-lumiere.png')) ?>">
        <meta
            name="description"
            content="Lumière Books - Temukan buku favoritmu dan perluas dunia melalui bacaan."
        >
        <title>
            <?= e(ucfirst($page === 'home' ? 'Lumière Books' : $page . ' | Lumière Books')) ?>
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
        <link rel="stylesheet" href="<?= e(asset('css/umum/3-tema-lumiere.css')) ?>?v=hero-foto-1">
        <?php if (in_array($page, ['login', 'register'], true)): ?>
            <link rel="stylesheet" href="<?= e(asset('css/pelanggan/halaman-masuk.css')) ?>">
            <?php endif; ?>
        </head>
        <body class="page-<?= e($page) ?>">
            <?php if (!in_array($page, ['welcome', 'login', 'register'], true)): ?>
                <?php if (!$user): ?>
                    <header class="guest-site-header">
                        <div class="guest-nav-wrap">
                            <a
                                class="guest-brand"
                                href="<?= e(url('welcome')) ?>"
                                aria-label="Lumière Books Beranda"
                            >
                                <img src="<?= e(asset('logo-lumiere.png')) ?>" alt="">
                                <span>
                        Lumière Books
                                </span>
                            </a>
                            <nav class="guest-nav" aria-label="Navigasi utama">
                                <a
                                    class="<?= in_array($page, ['home', 'welcome'], true) ? 'is-active' : '' ?>"
                                    href="<?= e(url('welcome')) ?>"
                                >
                        Beranda
                                </a>
                                <a
                                    class="<?= $page === 'about' ? 'is-active' : '' ?>"
                                    href="<?= e(url('about')) ?>"
                                >
                        Tentang Kami
                                </a>
                            </nav>
                            <div class="guest-tools" aria-label="Akses cepat">
                                <a
                                    href="<?= e(url('books')) ?>"
                                    aria-label="Cari buku"
                                    title="Cari buku"
                                >
                                <?= ui_icon('search') ?>
                                </a>
                                <a
                                    href="<?= e(url('login')) ?>"
                                    aria-label="Buku favorit"
                                    title="Buku favorit"
                                >
                                <?= ui_icon('heart') ?>
                                </a>
                                <a
                                    href="<?= e(url('login')) ?>"
                                    aria-label="Keranjang"
                                    title="Keranjang"
                                >
                                <?= ui_icon('cart') ?>
                                </a>
                            </div>
                        </div>
                    </header>
                    <?php else: ?>
                    <header class="site-header">
                        <div class="container nav-wrap">
                            <a class="brand" href="<?= e(url('home')) ?>">
                                <span class="brand-mark">
                                <img src="<?= e(asset('logo-lumiere.png')) ?>" alt="Lumière Books">
                                </span>
                                <span class="brand-name">
                        Lumière Books
                                <small>
                            Setiap Buku, Sejuta Cahaya.
                                </small>
                                </span>
                            </a>
                            <button
                                class="mobile-menu-toggle"
                                type="button"
                                aria-label="Buka menu"
                                data-menu-toggle
                            >
                    ☰
                            </button>
                            <nav class="main-nav" data-main-nav>
                                <a
                                    class="<?= $page === 'home' ? 'active' : '' ?>"
                                    href="<?= e(url('home')) ?>"
                                >
                        Beranda
                                </a>
                                <a href="<?= e(url('books', ['sort' => 'popular'])) ?>">
                        Buku Terlaris
                                </a>
                                <a href="<?= e(url('books', ['sort' => 'new'])) ?>">
                        Buku Baru
                                </a>
                                <a href="<?= e(url('about')) ?>">
                        Tentang Kami
                                </a>
                                <a href="<?= e(url('contact')) ?>">
                        Kontak
                                </a>
                            </nav>
                            <form class="nav-search" action="index.php" method="get">
                                <input type="hidden" name="page" value="books">
                                <?= ui_icon('search') ?>
                                <input
                                    type="search"
                                    name="q"
                                    placeholder="Cari buku, penulis, atau kategori..."
                                    value="<?= e($_GET['q'] ?? '') ?>"
                                >
                            </form>
                            <div class="nav-actions">
                                <a
                                    class="icon-link"
                                    href="<?= e(url('profile')) ?>"
                                    title="Profil saya"
                                >
                                <?= ui_icon('user') ?>
                                </a>
                                <?php if ($user['role'] === 'admin'): ?>
                                <a
                                    class="icon-link"
                                    href="<?= e(admin_url()) ?>"
                                    title="Dashboard admin"
                                >
                                <?= ui_icon('grid') ?>
                                </a>
                                <?php endif; ?>
                                <a class="cart-link" href="<?= e(url('cart')) ?>" title="Keranjang">
                                <?= ui_icon('cart') ?>
                                <span>
                                <?= cart_count() ?>
                                </span>
                                </a>
                                </div>
                            </div>
                        </header>
                        <?php endif; ?>
                        <?php endif; ?>
                        <main class="<?= $page === 'welcome' ? 'welcome-main' : '' ?>">
                            <?php if ($page !== 'welcome'): ?>
                                <div class="container flash-stack">
                                <?php foreach ($flashes as $flash): ?>
                                <div
                                    class="alert alert-<?= e($flash['type']) ?>"
                                    data-alert
                                >
                                <span>
                                <?= e($flash['message']) ?>
                                </span>
                                <button
                                    type="button"
                                    aria-label="Tutup"
                                    data-alert-close
                                >
                        ×
                                </button>
                                </div>
                                <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
