<div class="settings-layout">
    <nav class="settings-inner-nav">
        <a class="active" href="#store-info">
            <?= ui_icon('book-open') ?>
            Informasi Toko
        </a>
        <a href="#admin-account">
            <?= ui_icon('user') ?>
            Akun Admin
        </a>
        <a href="#security">
            <?= ui_icon('shield') ?>
            Keamanan
        </a>
        <a href="#notifications">
            <?= ui_icon('bell') ?>
            Notifikasi
        </a>
        <a href="#payment-demo">
            <?= ui_icon('wallet') ?>
            Pembayaran Demo
        </a>
        <a href="#store-preview">
            <?= ui_icon('image') ?>
            Pratinjau Website
        </a>
    </nav>
    <div class="settings-content">
        <section class="settings-website-card" id="store-preview">
            <div class="settings-website-card-copy">
                <span class="settings-website-card-icon">
                    <?= ui_icon('image') ?>
                </span>
                <span>
                    <b>
                        Pratinjau Website Lumière Books
                    </b>
                    <p>
                        Buka tampilan toko seperti yang dilihat pelanggan tanpa meninggalkan panel admin.
                    </p>
                </span>
            </div>
            <a
                class="button button-primary"
                href="<?= e(url('home')) ?>"
                target="_blank"
                rel="noopener"
            >
                <?= ui_icon('external-link') ?>
                Lihat Website
            </a>
        </section>
        <form
            method="post"
            action="<?= e(admin_url('settings')) ?>"
            class="settings-store-form"
        >
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_settings">
            <section class="settings-top-grid" id="store-info">
                <article class="admin-panel settings-info-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>
                                <?= ui_icon('book-open') ?>
                                Informasi Toko
                            </h2>
                            <p>
                                Atur informasi dasar toko Lumière Books.
                            </p>
                        </div>
                    </div>
                    <label>
                        Nama Toko *
                        <input
                            type="text"
                            name="store_name"
                            required
                            value="<?= e($settings['store_name']) ?>"
                        >
                    </label>
                    <label>
                        Deskripsi Singkat *
                        <textarea name="store_description" rows="3">
                            <?= e($settings['store_description']) ?>
                            </textarea>
                        </label>
                        <label>
                            Alamat Toko *
                            <input
                                type="text"
                                name="store_address"
                                value="<?= e($settings['store_address']) ?>"
                            >
                        </label>
                        <div class="form-grid-two">
                            <label>
                                Email Toko *
                                <input
                                    type="email"
                                    name="store_email"
                                    value="<?= e($settings['store_email']) ?>"
                                >
                            </label>
                            <label>
                                Nomor Telepon *
                                <input
                                    type="text"
                                    name="store_phone"
                                    value="<?= e($settings['store_phone']) ?>"
                                >
                            </label>
                        </div>
                        <div class="store-logo-setting">
                            <span class="store-logo-preview">
                                <img src="<?= e(asset('logo-lumiere.png')) ?>" alt="Logo Lumière Books">
                            </span>
                            <span>
                                <b>
                                Logo Toko
                                </b>
                                <small>
                                Logo resmi Lumière Books digunakan di seluruh halaman website dan panel admin.
                                </small>
                            </span>
                        </div>
                    </article>
                    <article class="admin-panel settings-hours-panel">
                        <div class="panel-heading">
                            <div>
                                <h2>
                                <?= ui_icon('clock') ?>
                                Jam Operasional
                                </h2>
                                <p>
                                Atur jam layanan toko.
                                </p>
                            </div>
                        </div>
                        <?php
                    $weekDays = [
                        'monday' => 'Senin',
    
                        'tuesday' => 'Selasa',
    
                        'wednesday' => 'Rabu',
    
                        'thursday' => 'Kamis',
    
                        'friday' => 'Jumat',
    
                        'saturday' => 'Sabtu',
    
                        'sunday' => 'Minggu',
    
                    ];

                    ?>
                        <?php foreach ($weekDays as $key => $label): ?>
                            <label class="hours-row">
                                <span>
                                <?= e($label) ?>
                                </span>
                                <input
                                    type="text"
                                    name="hours_<?= e($key) ?>"
                                    value="<?= e($settings['hours_' . $key]) ?>"
                                >
                            </label>
                            <?php endforeach; ?>
                            <button class="button button-primary button-full" type="submit">
                            Simpan Perubahan
                            </button>
                        </article>
                    </section>
                    <section class="settings-bottom-grid">
                        <article class="admin-panel" id="social-links">
                            <div class="panel-heading">
                                <div>
                                <h2>
                                <?= ui_icon('arrow-right') ?>
                                Kontak & Sosial Media
                                </h2>
                                <p>
                                Kelola tautan media sosial toko.
                                </p>
                                </div>
                            </div>
                            <label>
                            Facebook
                                <input
                                    type="url"
                                    name="store_facebook"
                                    value="<?= e($settings['store_facebook']) ?>"
                                    placeholder="https://facebook.com/..."
                                >
                            </label>
                            <label>
                            Instagram
                                <input
                                    type="url"
                                    name="store_instagram"
                                    value="<?= e($settings['store_instagram']) ?>"
                                    placeholder="https://instagram.com/..."
                                >
                            </label>
                            <label>
                            TikTok
                                <input
                                    type="url"
                                    name="store_tiktok"
                                    value="<?= e($settings['store_tiktok']) ?>"
                                    placeholder="https://tiktok.com/@..."
                                >
                            </label>
                            <label>
                            Twitter / X
                                <input
                                    type="url"
                                    name="store_x"
                                    value="<?= e($settings['store_x']) ?>"
                                    placeholder="https://x.com/..."
                                >
                            </label>
                            <label>
                            WhatsApp
                                <input
                                    type="text"
                                    name="store_whatsapp"
                                    value="<?= e($settings['store_whatsapp']) ?>"
                                    placeholder="08xxxxxxxxxx"
                                >
                            </label>
                        </article>
                        <article class="admin-panel" id="notifications">
                            <div class="panel-heading">
                                <div>
                                <h2>
                                <?= ui_icon('settings') ?>
                                Pengaturan Umum
                                </h2>
                                <p>
                                Atur preferensi tampilan dan bahasa.
                                </p>
                                </div>
                            </div>
                            <label class="setting-select-row">
                                <span>
                                Jumlah buku per halaman
                                </span>
                                <select name="books_per_page">
                                <?php foreach (['8', '10', '12', '16', '20'] as $amount): ?>
                                <option
                                    value="<?= e($amount) ?>"
                                    <?= $settings['books_per_page'] === $amount ? 'selected' : '' ?>
                                >
                                <?= e($amount) ?>
                                </option>
                                <?php endforeach; ?>
                                </select>
                                </label>
                                <label class="setting-select-row">
                                <span>
                                Mata uang
                                </span>
                                <select name="currency">
                                <option
                                    value="IDR"
                                    <?= $settings['currency'] === 'IDR' ? 'selected' : '' ?>
                                >
                                Rupiah (Rp)
                                </option>
                                </select>
                                </label>
                                <label class="setting-select-row">
                                <span>
                                Format tanggal
                                </span>
                                <select name="date_format">
                                <option
                                    value="d M Y"
                                    <?= $settings['date_format'] === 'd M Y' ? 'selected' : '' ?>
                                >
                                DD MMM YYYY
                                </option>
                                <option
                                    value="d/m/Y"
                                    <?= $settings['date_format'] === 'd/m/Y' ? 'selected' : '' ?>
                                >
                                DD/MM/YYYY
                                </option>
                                </select>
                                </label>
                                <label class="setting-select-row">
                                <span>
                                Bahasa
                                </span>
                                <select name="language">
                                <option
                                    value="id"
                                    <?= $settings['language'] === 'id' ? 'selected' : '' ?>
                                >
                                Indonesia
                                </option>
                                </select>
                                </label>
                                <label class="maintenance-toggle">
                                <span>
                                <b>
                                Mode Maintenance
                                </b>
                                <small>
                                Nonaktif secara default untuk kebutuhan praktik USK.
                                </small>
                                </span>
                                <input
                                    type="checkbox"
                                    name="maintenance_mode"
                                    value="1"
                                    <?= $settings['maintenance_mode'] === '1' ? 'checked' : '' ?>
                                >
                                </label>
                            </article>
                            <article class="admin-panel" id="payment-demo">
                                <div class="panel-heading">
                                <div>
                                <h2>
                                <?= ui_icon('wallet') ?>
                                Pembayaran Demo
                                </h2>
                                <p>
                                Mode simulasi pembayaran untuk praktik USK.
                                </p>
                                </div>
                                <span class="status-badge status-success">
                                Aktif
                                </span>
                                </div>
                                <div class="payment-setting-card">
                                <span class="payment-setting-icon">
                                <?= ui_icon('shield') ?>
                                </span>
                                <div>
                                <b>
                                Mode Simulasi USK
                                </b>
                                <small>
                                Tidak memerlukan API key atau akun merchant.
                                </small>
                                </div>
                                </div>
                                <ul class="instruction-list">
                                <li>
                                Transfer Bank: BCA dan BRI (simulasi).
                                </li>
                                <li>
                                COD: bayar kepada kurir ketika pesanan diterima.
                                </li>
                                <li>
                                COD: pembayaran dicatat admin setelah diterima.
                                </li>
                                </ul>
                                <p class="inline-notice">
                            Semua transaksi hanya simulasi. Tidak ada uang sungguhan yang dipotong atau
                            dikirim.
                                </p>
                            </article>
                        </section>
                        <div class="settings-save-row">
                            <button class="button button-primary" type="submit">
                        Simpan Semua Pengaturan
                            </button>
                        </div>
                    </form>
                    <section class="admin-panel password-setting-panel" id="security">
                        <div class="panel-heading">
                            <div>
                                <h2>
                                <?= ui_icon('shield') ?>
                            Ubah Password Admin
                                </h2>
                                <p>
                            Ganti password akun administrator.
                                </p>
                            </div>
                        </div>
                        <form
                            method="post"
                            action="<?= e(admin_url('settings')) ?>"
                        >
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="update_admin_password">
                            <div class="form-grid-three">
                                <label>
                            Password Lama *
                                <input
                                    type="password"
                                    name="current_password"
                                    required
                                    placeholder="Masukkan password lama..."
                                >
                                </label>
                                <label>
                            Password Baru *
                                <input
                                    type="password"
                                    name="new_password"
                                    required
                                    minlength="8"
                                    placeholder="Masukkan password baru..."
                                >
                                </label>
                                <label>
                            Konfirmasi Password Baru *
                                <input
                                    type="password"
                                    name="confirm_password"
                                    required
                                    minlength="8"
                                    placeholder="Ulangi password baru..."
                                >
                                </label>
                            </div>
                            <button class="button button-primary" type="submit">
                        Simpan Password
                            </button>
                        </form>
                    </section>
                    <section class="admin-panel admin-account-panel" id="admin-account">
                        <div>
                            <span class="admin-account-avatar">
                                <?= ui_icon('user') ?>
                            </span>
                            <span>
                                <b>
                                <?= e($admin['name']) ?>
                                </b>
                                <small>
                                <?= e($admin['email']) ?>
                            · Administrator
                                </small>
                            </span>
                        </div>
                        <a class="button button-outline" href="#security">
                    Kelola Keamanan
                        </a>
                    </section>
                </div>
            </div>
