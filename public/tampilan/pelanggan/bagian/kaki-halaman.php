</main>
<?php if (!in_array($page, ['login', 'register'], true)): ?>
    <footer class="site-footer">
        <div class="container footer-benefits">
            <div>
                <span class="benefit-icon">
                    <?= ui_icon('book-open') ?>
                </span>
                <span>
                    <b>
                    Buku Original 100%
                    </b>
                    <small>
                    Koleksi pilihan berkualitas
                    </small>
                </span>
            </div>
            <div>
                <span class="benefit-icon">
                    <?= ui_icon('shield-check') ?>
                </span>
                <span>
                    <b>
                    Pembayaran Aman
                    </b>
                    <small>
                    BCA, BRI, QRIS, dan COD
                    </small>
                </span>
            </div>
            <div>
                <span class="benefit-icon">
                    <?= ui_icon('truck') ?>
                </span>
                <span>
                    <b>
                    Pengiriman Cepat
                    </b>
                    <small>
                    Ke seluruh Indonesia
                    </small>
                </span>
            </div>
            <div>
                <span class="benefit-icon">
                    <?= ui_icon('headset') ?>
                </span>
                <span>
                    <b>
                    Layanan Pelanggan
                    </b>
                    <small>
                    Siap membantu kamu
                    </small>
                </span>
            </div>
        </div>
        <div class="footer-main container">
            <div class="footer-brand">
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
                <p>
                Temukan cerita, pengetahuan, dan inspirasi dalam setiap halaman.
                </p>
                <div class="social-links">
                    <a href="#">
                    ◎
                    </a>
                    <a href="#">
                    ♪
                    </a>
                    <a href="#">
                    ▶
                    </a>
                    <a href="#">
                    𝕏
                    </a>
                </div>
            </div>
            <div>
                <h4>
                Tautan Cepat
                </h4>
                <a href="<?= e(url($user ? 'home' : 'welcome')) ?>">
                Beranda
                </a>
                <a href="<?= e(url('books')) ?>">
                Cari Buku
                </a>
                <a href="<?= e(url('books', ['sort' => 'new'])) ?>">
                Buku Baru
                </a>
                <a href="<?= e(url('about')) ?>">
                Tentang Kami
                </a>
            </div>
            <div>
                <h4>
                Layanan Pelanggan
                </h4>
                <a href="<?= e(url('help')) ?>">
                Pusat Bantuan
                </a>
                <a href="<?= e(url('my-orders')) ?>">
                Pesanan Saya
                </a>
                <a href="<?= e(url('contact')) ?>">
                Pengiriman
                </a>
                <a href="<?= e(url('contact')) ?>">
                Retur & Pengembalian
                </a>
            </div>
            <div class="footer-payment">
                <h4>
                Metode Pembayaran
                </h4>
                <p>
                Pilih pembayaran yang praktis dan aman saat checkout.
                </p>
                <div class="payment-brand-group">
                    <small>
                    TRANSFER BANK
                    </small>
                    <div class="payment-brand-row">
                        <span class="payment-brand bank-bca">
                        BCA
                        </span>
                        <span class="payment-brand bank-bri">
                        BRI
                        </span>
                    </div>
                </div>
                <div class="payment-brand-group">
                    <small>
                    DOMPET DIGITAL / QRIS
                    </small>
                    <div class="payment-brand-row">
                        <span class="payment-brand wallet-dana">
                        DANA
                        </span>
                        <span class="payment-brand wallet-ovo">
                        OVO
                        </span>
                        <span class="payment-brand wallet-gopay">
                        GoPay
                        </span>
                    </div>
                </div>
                <small class="payment-demo-note">
                Pilihan pembayaran tersedia saat checkout.
                </small>
            </div>
        </div>
        <div class="container footer-bottom">
        ©
            <?= date('Y') ?>
        Lumière Books. All Rights Reserved.
        </div>
    </footer>
    <?php endif; ?>
    <script src="<?= e(asset('js/umum/skrip-utama.js')) ?>
?v=cart-ajax-3">
</script>
    </body>
    </html>
