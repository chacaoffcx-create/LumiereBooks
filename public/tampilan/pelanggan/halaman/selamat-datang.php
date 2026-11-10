<?php
$welcomeSearch = trim((string) ($_GET['q'] ?? ''));
?>
<header class="welcome-topbar">
    <a class="welcome-brand" href="<?= e(url('welcome')) ?>" aria-label="Lumière Books Beranda">
        <img src="<?= e(asset('logo-lumiere.png')) ?>" alt="">
        <span>
            Lumière Books
        </span>
    </a>
    <nav class="welcome-nav" aria-label="Navigasi utama">
        <a class="is-active" href="<?= e(url('welcome')) ?>">
            Beranda
        </a>
        <a href="<?= e(url('about')) ?>">
            Tentang Kami
        </a>
    </nav>
    <div class="welcome-tools" aria-label="Akses cepat">
        <a class="welcome-auth" href="<?= e(url('login')) ?>">Masuk</a>
        <a class="welcome-auth welcome-auth-fill" href="<?= e(url('register')) ?>">Daftar</a>
        <a href="<?= e(url('books')) ?>" aria-label="Cari buku" title="Cari buku">
            <?= ui_icon('search') ?>
        </a>
        <a href="<?= e(url('login')) ?>" aria-label="Buku favorit" title="Buku favorit">
            <?= ui_icon('heart') ?>
        </a>
        <a href="<?= e(url('login')) ?>" aria-label="Keranjang" title="Keranjang">
            <?= ui_icon('cart') ?>
        </a>
    </div>
</header>
<section class="welcome-fullscreen" aria-label="Selamat datang di Lumière Books">
    <div
        class="welcome-fullscreen-bg"
        role="img"
        aria-label="Suasana meja baca hangat dengan koleksi buku dan perlengkapan membaca"
    >
    </div>
    <div class="welcome-copy">
        <h1>
            Setiap Buku,
            <br>
            Sejuta Cahaya.
        </h1>
        <div class="welcome-flourish" aria-hidden="true">
            <span>
            </span>
            <b>
                ✦
            </b>
        </div>
        <p>
            Temukan berbagai koleksi buku, alat tulis,
            <br class="desktop-break">
            dan hadiah pilihan untuk menemani
            <br class="desktop-break">
            setiap langkah dan inspirasimu.
        </p>
        <a class="welcome-enter" href="<?= e(url('login')) ?>">
            Masuk
            <span aria-hidden="true">
                →
            </span>
        </a>
    </div>
    <div class="welcome-trustbar" aria-label="Keunggulan Lumière Books">
        <div class="welcome-trust-item">
            <span class="welcome-trust-icon"><?= ui_icon('truck') ?></span>
            <span class="welcome-trust-text">Pengiriman<br><strong>Aman &amp; Cepat</strong></span>
        </div>
        <div class="welcome-trust-item">
            <span class="welcome-trust-icon"><?= ui_icon('shield') ?></span>
            <span class="welcome-trust-text">Pembayaran<br><strong>Aman (COD)</strong></span>
        </div>
        <div class="welcome-trust-item">
            <span class="welcome-trust-icon"><?= ui_icon('book-open') ?></span>
            <span class="welcome-trust-text">Buku Original<br><strong>100%</strong></span>
        </div>
    </div>
</section>
<?php include dirname(__DIR__) . '/bagian/isi-beranda.php'; ?>
