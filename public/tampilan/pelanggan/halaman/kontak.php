<section class="container page-section contact-page">
    <div class="contact-hero">
        <div class="contact-hero-copy">
            <span class="eyebrow">
                KAMI SIAP MEMBANTU
            </span>
            <h1>
                Kontak Kami
            </h1>
            <p>
                Punya pertanyaan, saran, atau kendala? Hubungi tim Lumière Books, kami siap membantu kamu
                dengan senang hati.
            </p>
        </div>
    </div>
    <nav class="contact-breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(url($user ? 'home' : 'welcome')) ?>">
            Beranda
        </a>
        <span>
            ›
        </span>
        <strong>
            Kontak Kami
        </strong>
    </nav>
    <div class="contact-redesign-grid">
        <aside class="contact-details-card">
            <h2>
                Hubungi Kami
            </h2>
            <div class="contact-detail">
                <span class="contact-detail-icon">
                    <?= ui_icon('mail') ?>
                </span>
                <div>
                    <b>
                        Email
                    </b>
                    <a href="mailto:<?= e(app_setting('store_email', 'halo@lumierebooks.id')) ?>">
                        <?= e(app_setting('store_email', 'halo@lumierebooks.id')) ?>
                    </a>
                </div>
            </div>
            <div class="contact-detail">
                <span class="contact-detail-icon">
                    <?= ui_icon('message-circle') ?>
                </span>
                <div>
                    <b>
                        WhatsApp
                    </b>
                    <a
                        href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string) app_setting('store_phone', '0812 0000 0000'))) ?>"
                    >
                        <?= e(app_setting('store_phone', '0812 0000 0000')) ?>
                    </a>
                </div>
            </div>
            <div class="contact-detail">
                <span class="contact-detail-icon">
                    <?= ui_icon('phone') ?>
                </span>
                <div>
                    <b>
                        Telepon
                    </b>
                    <span>
                        <?= e(app_setting('store_phone', '0812 0000 0000')) ?>
                    </span>
                </div>
            </div>
            <div class="contact-detail">
                <span class="contact-detail-icon">
                    <?= ui_icon('map-pin') ?>
                </span>
                <div>
                    <b>
                        Alamat
                    </b>
                    <span>
                        <?= e(app_setting('store_address', 'Indonesia')) ?>
                    </span>
                </div>
            </div>
            <div
                class="contact-side-photo"
                role="img"
                aria-label="Sudut membaca hangat dengan buku dan cangkir"
            >
            </div>
        </aside>
        <div class="contact-main-column">
            <form class="contact-form-card" method="post" action="<?= e(url('contact')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="contact">
                <input type="hidden" name="_back" value="<?= e(url('contact')) ?>">
                <div class="contact-form-heading">
                    <div>
                        <h2>
                            Kirim Pesan untuk Kami
                        </h2>
                        <p>
                            Tulis pesanmu melalui formulir di bawah ini. Kami akan segera merespons dalam 1 ×
                            24 jam.
                        </p>
                    </div>
                </div>
                <div class="contact-form-fields">
                    <label class="contact-input-wrap">
                        <span class="contact-field-icon">
                            <?= ui_icon('user') ?>
                        </span>
                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="Nama Lengkap"
                            aria-label="Nama Lengkap"
                        >
                    </label>
                    <label class="contact-input-wrap">
                        <span class="contact-field-icon">
                            <?= ui_icon('mail') ?>
                        </span>
                        <input
                            type="email"
                            name="email"
                            required
                            placeholder="Email"
                            aria-label="Email"
                        >
                    </label>
                    <label class="contact-input-wrap">
                        <span class="contact-field-icon">
                            <?= ui_icon('phone') ?>
                        </span>
                        <input
                            type="tel"
                            name="whatsapp"
                            placeholder="Nomor WhatsApp (Opsional)"
                            aria-label="Nomor WhatsApp (Opsional)"
                        >
                    </label>
                    <label class="contact-input-wrap">
                        <span class="contact-field-icon">
                            <?= ui_icon('tag') ?>
                        </span>
                        <select name="subject" required aria-label="Pilih Topik">
                            <option value="" selected disabled>
                                Pilih Topik
                            </option>
                            <option value="Pertanyaan Produk">
                                Pertanyaan Produk
                            </option>
                            <option value="Pesanan dan Pengiriman">
                                Pesanan dan Pengiriman
                            </option>
                            <option value="Pembayaran">
                                Pembayaran
                            </option>
                            <option value="Pengembalian">
                                Pengembalian
                            </option>
                            <option value="Saran dan Masukan">
                                Saran dan Masukan
                            </option>
                            <option value="Lainnya">
                                Lainnya
                            </option>
                        </select>
                    </label>
                    <label class="contact-message-wrap">
                        <span class="contact-field-icon">
                            <?= ui_icon('message-circle') ?>
                        </span>
                        <textarea
                            name="message"
                            rows="5"
                            required
                            placeholder="Tulis pesanmu di sini..."
                            aria-label="Pesan"
                        >
                            </textarea>
                        </label>
                    </div>
                    <button class="contact-submit-button" type="submit">
                        <span>
                            <?= ui_icon('send') ?>
                        </span>
                        Kirim Pesan
                    </button>
                </form>
                <a class="contact-help-banner" href="<?= e(url('help')) ?>">
                    <span class="contact-help-art" aria-hidden="true">
                        <?= ui_icon('message-circle') ?>
                    </span>
                    <span class="contact-help-copy">
                        <b>
                            Pertanyaan Umum?
                        </b>
                        <small>
                            Temukan jawaban atas pertanyaan yang sering diajukan seputar pemesanan,
                            pembayaran, pengiriman, dan lainnya di halaman
                            <strong>
                                Pusat Bantuan
                            </strong>
                            .
                        </small>
                    </span>
                    <span class="contact-help-arrow" aria-hidden="true">
                        →
                    </span>
                </a>
            </div>
        </div>
    </section>
