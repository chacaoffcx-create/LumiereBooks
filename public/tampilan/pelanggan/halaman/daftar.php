<section class="lumiere-login-page lumiere-register-page">
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
                    Buat Akun Baru
                </h1>
                <p>
                    Mulai perjalanan membaca yang lebih menyenangkan bersama Lumière Books.
                </p>
            </header>
            <form
                class="lumiere-login-form lumiere-register-form"
                action="<?= e(url('register')) ?>"
                method="post"
            >
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="_back" value="<?= e(url('register')) ?>">
                <label class="lumiere-login-field">
                    <span class="field-icon" aria-hidden="true">
                        ♙
                    </span>
                    <input
                        type="text"
                        name="name"
                        required
                        autocomplete="name"
                        placeholder="Nama Lengkap"
                        aria-label="Nama Lengkap"
                        value="<?= e($_POST['name'] ?? '') ?>"
                    >
                </label>
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
                        ☎
                    </span>
                    <input
                        type="tel"
                        name="phone"
                        autocomplete="tel"
                        placeholder="Nomor WhatsApp (opsional)"
                        aria-label="Nomor WhatsApp (opsional)"
                        value="<?= e($_POST['phone'] ?? '') ?>"
                    >
                </label>
                <label class="lumiere-login-field">
                    <span class="field-icon" aria-hidden="true">
                        ♙
                    </span>
                    <input
                        id="lumiere-register-password"
                        type="password"
                        name="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Kata Sandi (minimal 8 karakter)"
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
                <button class="lumiere-login-submit" type="submit">
                    <span>
                        Daftar Sekarang
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
                <a class="lumiere-register-link" href="<?= e(url('login')) ?>">
                    <span aria-hidden="true">
                        ↩
                    </span>
                    Sudah Punya Akun? Masuk
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
    const password = document.getElementById('lumiere-register-password');
    if (toggle && password) {
        toggle.addEventListener('click', function () {
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    }
});
</script>
