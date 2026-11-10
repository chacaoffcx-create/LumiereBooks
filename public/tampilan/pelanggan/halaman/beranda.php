<section class="home-hero container lumiere-hero">
    <img
        class="home-hero-banner"
        src="<?= e(asset('images/hero-banner.jpg')) ?>"
        alt="Koleksi buku Lumière Books"
    >
    <div class="lumiere-hero-copy">
        <span class="eyebrow">
            SELAMAT DATANG DI
        </span>
        <h1>
            Lumière Books
        </h1>
        <h2>
            Setiap Buku, Sejuta Cahaya.
        </h2>
        <p>
            Temukan ribuan buku favoritmu dari berbagai genre. Bacaan terbaik untuk setiap langkahmu.
        </p>
        <div class="hero-trust-points" aria-label="Keunggulan Lumière Books">
            <div class="hero-trust-item">
                <span class="hero-trust-icon">
                    <?= ui_icon('truck') ?>
                </span>
                <span>
                    Pengiriman
                    <br>
                    <strong>
                        Aman &amp; Cepat
                    </strong>
                </span>
            </div>
            <div class="hero-trust-item">
                <span class="hero-trust-icon">
                    <?= ui_icon('shield') ?>
                </span>
                <span>
                    Pembayaran
                    <br>
                    <strong>
                        Aman (COD)
                    </strong>
                </span>
            </div>
            <div class="hero-trust-item">
                <span class="hero-trust-icon">
                    <?= ui_icon('book-open') ?>
                </span>
                <span>
                    Buku Original
                    <br>
                    <strong>
                        100%
                    </strong>
                </span>
            </div>
        </div>
    </div>
</section>
<?php include dirname(__DIR__) . '/bagian/isi-beranda.php'; ?>
