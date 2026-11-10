<section class="lumiere-login-page">
    <div
        class="lumiere-login-photo"
        role="img"
        aria-label="Sudut membaca yang hangat dengan buku dan lampu"
    >
    </div>
    <div class="lumiere-login-panel">
        <div class="lumiere-login-content">
            <a class="lumiere-login-brand" href="<?= e(url('welcome')) ?>">
                <img src="<?= e(asset('logo-lumiere.png')) ?>" alt="Logo Lumière Books">
                <span>
                    Lumière Books
                </span>
            </a>
            <header class="lumiere-login-heading">
                <h1>
                    Masuk ke Akun Anda
                </h1>
                <p>
                    Selamat datang kembali! Silakan masuk untuk melanjutkan
                    <br class="desktop-break">
                    belanja buku di Lumière Books.
                </p>
            </header>
            <form class="lumiere-login-form" action="<?= e(url('login')) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="_back" value="<?= e(url('login')) ?>">
                <label class="lumiere-login-field">
                    <span class="field-icon" aria-hidden="true">
                        <?= ui_icon('mail') ?>
                    </span>
                    <input
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="Email"
                        aria-label="Email"
                        value="<?= e($_POST['email'] ?? '') ?>"
                    >
                </label>
                <label class="lumiere-login-field">
                    <span class="field-icon" aria-hidden="true">
                        <?= ui_icon('lock') ?>
                    </span>
                    <input
                        id="lumiere-login-password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Kata Sandi"
                        aria-label="Kata Sandi"
                    >
                    <button
                        class="password-visibility"
                        type="button"
                        aria-label="Tampilkan kata sandi"
                        data-password-toggle
                    >
                        <?= ui_icon('eye') ?>
                    </button>
                </label>
                <div class="lumiere-login-options">
                    <label class="remember-option">
                        <input type="checkbox" name="remember" value="1">
                        <span class="custom-check" aria-hidden="true">
                        </span>
                        <span>
                            Ingat saya
                        </span>
                    </label>
                    <a href="<?= e(url('contact', ['topic' => 'lupa-kata-sandi'])) ?>">
                        Lupa kata sandi?
                    </a>
                </div>
                <button class="lumiere-login-submit" type="submit">
                    <span>
                        Masuk
                    </span>
                    <span aria-hidden="true">
                        →
                    </span>
                </button>
                <div class="lumiere-login-divider">
                    <span>
                        atau
                    </span>
                </div>
                <a class="lumiere-register-link" href="<?= e(url('register')) ?>">
                    <svg
                        class="login-user-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <rect
                            x="8"
                            y="2.5"
                            width="8"
                            height="9"
                            rx="2.2"
                         />
                        <path d="M3 21c.5-4.5 3.7-7 9-7s8.5 2.5 9 7"/>
                    </svg>
                    Buat Akun Baru
                </a>
            </form>
        </div>
        <div class="lumiere-login-decoration" aria-hidden="true">
            <span class="decoration-leaf">
                ✦
            </span>
            <span class="decoration-books">
                ▱
            </span>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-password-toggle]');
    const password = document.getElementById('lumiere-login-password');
    if (toggle && password) {
        toggle.addEventListener('click', function () {
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    }
});
</script>
