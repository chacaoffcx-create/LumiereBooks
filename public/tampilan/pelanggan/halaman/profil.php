<?php
$avatarPath = trim((string) ($user['avatar'] ?? ''));

$avatarUrl = $avatarPath !== '' ? avatar_url($avatarPath) : '';

$genderValue = (string) ($user['gender'] ?? '');

?>
<section class="container account-layout profile-page-layout">
    <?php $accountActive = 'profile'; include dirname(__DIR__) . '/bagian/menu-akun.php'; ?>
    <div class="account-main profile-main">
        <h1>
            Profil Saya
        </h1>
        <p>
            Kelola informasi akun dan data pribadi Anda di Lumière Books.
        </p>
        <section class="profile-summary profile-summary-redesign">
            <div class="profile-avatar-wrap">
                <div class="avatar-circle profile-avatar-image">
                    <?php if ($avatarUrl !== ''): ?>
                        <img src="<?= e($avatarUrl) ?>" alt="Foto profil <?= e($user['name']) ?>">
                        <?php else: ?>
                        <span>
                            <?= e(strtoupper(substr((string) $user['name'], 0, 1))) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <form
                        class="profile-avatar-upload"
                        method="post"
                        action="<?= e(url('profile')) ?>"
                        enctype="multipart/form-data"
                    >
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="upload_avatar">
                        <input type="hidden" name="_back" value="<?= e(url('profile')) ?>">
                        <label
                            class="profile-camera-button"
                            for="profile-avatar-file"
                            title="Pilih foto profil"
                            aria-label="Pilih foto profil"
                        >
                            <?= ui_icon('camera') ?>
                        </label>
                        <input
                            id="profile-avatar-file"
                            class="profile-avatar-file"
                            type="file"
                            name="avatar"
                            accept="image/jpeg,image/png,image/webp"
                            required
                        >
                        <button class="profile-avatar-submit" type="submit">
                        Unggah Foto
                        </button>
                    </form>
                </div>
                <div class="profile-summary-copy">
                    <h2>
                        <?= e($user['name']) ?>
                    </h2>
                    <p class="profile-contact-line">
                        <?= ui_icon('mail') ?>
                        <span>
                            <?= e($user['email']) ?>
                        </span>
                    </p>
                    <p class="profile-contact-line">
                        <?= ui_icon('message-circle') ?>
                        <span>
                            <?= e($user['phone'] ?: 'Nomor WhatsApp belum diisi') ?>
                        </span>
                    </p>
                    <span class="soft-pill">
                    Pelanggan
                    </span>
                </div>
                <div class="profile-joined">
                    <?= ui_icon('calendar') ?>
                    <span>
                    Bergabung sejak
                        <br>
                        <strong>
                            <?= e(date_id($user['created_at'] ?? date('Y-m-d'))) ?>
                        </strong>
                    </span>
                </div>
            </section>
            <nav class="profile-tabs profile-tabs-redesign" aria-label="Pengaturan profil">
                <button class="selected" type="button" data-profile-tab="profile-info">
                    <?= ui_icon('user') ?>
                    <span>
                    Informasi Pribadi
                    </span>
                </button>
                <button type="button" data-profile-tab="profile-password">
                    <?= ui_icon('lock') ?>
                    <span>
                    Ubah Kata Sandi
                    </span>
                </button>
            </nav>
            <form
                class="account-form profile-form-card"
                id="profile-info"
                method="post"
                action="<?= e(url('profile')) ?>"
            >
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save_profile">
                <input type="hidden" name="_back" value="<?= e(url('profile')) ?>">
                <h2>
                    <?= ui_icon('user') ?>
                    <span>
                    Informasi Pribadi
                    </span>
                </h2>
                <label>
                    <span>
                    Nama Lengkap
                        <b>
                        *
                        </b>
                    </span>
                    <input
                        name="name"
                        required
                        maxlength="120"
                        value="<?= e($user['name']) ?>"
                    >
                </label>
                <label>
                    <span>
                    Email
                        <b>
                        *
                        </b>
                    </span>
                    <input
                        type="email"
                        name="email"
                        required
                        value="<?= e($user['email']) ?>"
                    >
                </label>
                <label>
                    <span>
                    Nomor WhatsApp
                        <b>
                        *
                        </b>
                    </span>
                    <input
                        name="phone"
                        required
                        maxlength="30"
                        value="<?= e($user['phone'] ?? '') ?>"
                    >
                </label>
                <label>
                    <span>
                    Tanggal Lahir
                    </span>
                    <input type="date" name="birth_date" value="<?= e($user['birth_date'] ?? '') ?>">
                </label>
                <fieldset class="profile-gender-field">
                    <legend>
                    Jenis Kelamin
                    </legend>
                    <label>
                        <input
                            type="radio"
                            name="gender"
                            value="female"
                            <?= $genderValue === 'female' ? 'checked' : '' ?>
                        >
                        <span>
                        Perempuan
                        </span>
                    </label>
                    <label>
                        <input
                            type="radio"
                            name="gender"
                            value="male"
                            <?= $genderValue === 'male' ? 'checked' : '' ?>
                        >
                        <span>
                        Laki-laki
                        </span>
                    </label>
                    <label class="profile-gender-other">
                        <input
                            type="radio"
                            name="gender"
                            value="other"
                            <?= $genderValue === 'other' ? 'checked' : '' ?>
                        >
                        <span>
                        Tidak ingin menyebutkan
                        </span>
                    </label>
                </fieldset>
                <button class="button button-primary profile-save-button" type="submit">
                    <?= ui_icon('save') ?>
                    <span>
                    Simpan Perubahan
                    </span>
                </button>
            </form>
            <form
                class="account-form profile-form-card profile-password-card"
                id="profile-password"
                method="post"
                action="<?= e(url('profile')) ?>"
                hidden
            >
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="change_password">
                <input type="hidden" name="_back" value="<?= e(url('profile')) ?>">
                <h2>
                    <?= ui_icon('lock') ?>
                    <span>
                    Ubah Kata Sandi
                    </span>
                </h2>
                <label>
                    <span>
                    Kata Sandi Lama
                    </span>
                    <input
                        type="password"
                        name="old_password"
                        autocomplete="current-password"
                        required
                    >
                </label>
                <label>
                    <span>
                    Kata Sandi Baru
                    </span>
                    <input
                        type="password"
                        name="new_password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                </label>
                <label>
                    <span>
                    Konfirmasi Kata Sandi
                    </span>
                    <input
                        type="password"
                        name="confirm_password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                </label>
                <button class="button button-primary profile-save-button" type="submit">
                    <?= ui_icon('save') ?>
                    <span>
                    Perbarui Kata Sandi
                    </span>
                </button>
            </form>
        </div>
    </section>
    <script>
(() => {
    const tabs = document.querySelectorAll('[data-profile-tab]');
    const panels = ['profile-info', 'profile-password'].map(id => document.getElementById(id));
    tabs.forEach(tab => tab.addEventListener('click', () => {
        const target = tab.dataset.profileTab;
        panels.forEach(panel => {
            if (panel) panel.hidden = panel.id !== target;
        });
        tabs.forEach(item => item.classList.toggle('selected', item === tab));
    }));
    const fileInput = document.getElementById('profile-avatar-file');
    const uploadForm = document.querySelector('.profile-avatar-upload');
    const submit = document.querySelector('.profile-avatar-submit');
    if (fileInput && uploadForm && submit) {
        fileInput.addEventListener('change', () => {
            submit.classList.toggle('is-visible', fileInput.files.length > 0);
        });
    }
})();
</script>
