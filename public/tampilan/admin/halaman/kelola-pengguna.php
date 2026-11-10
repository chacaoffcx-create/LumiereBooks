<section class="admin-panel admin-form-panel" style="margin-bottom:24px;">
    <div class="panel-heading">
        <div>
            <h2>
                Tambah Administrator
            </h2>
            <p>
                Buat akun pengelola baru. Hanya administrator yang sudah masuk yang dapat menggunakan formulir
                ini.
            </p>
        </div>
    </div>
    <form method="post" action="<?= e(admin_url('users')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create_admin">
        <label>
            Nama Administrator
            <input
                type="text"
                name="admin_name"
                required
                maxlength="120"
                placeholder="Nama lengkap"
            >
        </label>
        <label>
            Email Administrator
            <input
                type="email"
                name="admin_email"
                required
                maxlength="190"
                placeholder="admin@email.com"
            >
        </label>
        <label>
            Kata Sandi (minimal 10 karakter)
            <input
                type="password"
                name="admin_password"
                required
                minlength="10"
                autocomplete="new-password"
            >
        </label>
        <label>
            Konfirmasi Kata Sandi
            <input
                type="password"
                name="admin_password_confirm"
                required
                minlength="10"
                autocomplete="new-password"
            >
        </label>
        <button class="button button-primary" type="submit">
            <?= ui_icon('plus') ?>
            Buat Akun Administrator
        </button>
    </form>
</section>
<section class="admin-stat-grid users-stat-grid">
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('user') ?>
        </span>
        <div>
            <small>
                Total User
            </small>
            <b>
                <?= number_format($userStats['total'], 0, ',', '.') ?>
            </b>
            <em>
                Pengguna terdaftar
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('users') ?>
        </span>
        <div>
            <small>
                User Aktif
            </small>
            <b>
                <?= number_format($userStats['active'], 0, ',', '.') ?>
            </b>
            <em>
                Akun aktif
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon stat-icon-warning">
            <?= ui_icon('user') ?>
        </span>
        <div>
            <small>
                User Tidak Aktif
            </small>
            <b>
                <?= number_format($userStats['inactive'], 0, ',', '.') ?>
            </b>
            <em>
                Akun dinonaktifkan
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('star') ?>
        </span>
        <div>
            <small>
                User Baru (Bulan Ini)
            </small>
            <b>
                <?= number_format($userStats['new'], 0, ',', '.') ?>
            </b>
            <em>
                Pendaftaran bulan ini
            </em>
        </div>
    </article>
</section>
<section class="admin-panel user-table-panel">
    <form class="admin-filter redesign-filter user-filter" method="get" action="admin.php">
        <input type="hidden" name="page" value="users">
        <label class="filter-search">
            <?= ui_icon('search') ?>
            <input
                type="search"
                name="q"
                value="<?= e($userSearch) ?>"
                placeholder="Cari nama, email, atau ID user..."
            >
        </label>
        <select name="status" aria-label="Filter status pengguna">
            <option value="">
                Semua Status
            </option>
            <option value="active" <?= $userStatusFilter === 'active' ? 'selected' : '' ?>>
                Aktif
            </option>
            <option value="inactive" <?= $userStatusFilter === 'inactive' ? 'selected' : '' ?>>
                Tidak Aktif
            </option>
        </select>
        <button class="button button-outline button-small" type="submit">
            <?= ui_icon('filter') ?>
            Filter
        </button>
        <a class="button button-outline button-small" href="<?= e(admin_url('users')) ?>">
            Reset
        </a>
    </form>
    <div class="table-wrap">
        <table class="data-table redesign-table user-data-table">
            <thead>
                <tr>
                    <th>
                        No
                    </th>
                    <th>
                        Nama
                    </th>
                    <th>
                        Email
                    </th>
                    <th>
                        Role
                    </th>
                    <th>
                        Tanggal Daftar
                    </th>
                    <th>
                        Total Pesanan
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td>
                            <?= $index + 1 ?>
                        </td>
                        <td>
                            <span class="user-name-cell">
                                <span class="user-table-avatar">
                                <?= ui_icon('user') ?>
                                </span>
                                <b>
                                <?= e($user['name']) ?>
                                </b>
                            </span>
                        </td>
                        <td>
                            <?= e($user['email']) ?>
                        </td>
                        <td>
                            <span class="category-pill">
                            User
                            </span>
                        </td>
                        <td>
                            <?= date_id($user['created_at'], true) ?>
                        </td>
                        <td>
                            <?= number_format((int) $user['order_count'], 0, ',', '.') ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= e(status_class($user['status'])) ?>">
                                <?= e($user['status'] === 'active' ? 'Aktif' : 'Tidak Aktif') ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button
                                    class="action-button"
                                    type="button"
                                    title="Lihat detail pengguna"
                                    data-user-detail
                                    data-user-name="<?= e($user['name']) ?>"
                                    data-user-email="<?= e($user['email']) ?>"
                                    data-user-phone="<?= e($user['phone'] ?: '-') ?>"
                                    data-user-created="<?= e(date_id($user['created_at'], true)) ?>"
                                    data-user-orders="<?= (int) $user['order_count'] ?>"
                                >
                                <?= ui_icon('eye') ?>
                                </button>
                                <form
                                    method="post"
                                    action="<?= e(admin_url('users')) ?>"
                                    data-confirm="Ubah status akun pengguna ini?"
                                >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="toggle_user">
                                <input
                                    type="hidden"
                                    name="user_id"
                                    value="<?= (int) $user['id'] ?>"
                                >
                                <button
                                    class="action-button"
                                    type="submit"
                                    title="<?= $user['status'] === 'active' ? 'Nonaktifkan' : 'Aktifkan' ?> akun"
                                >
                                <?= ui_icon('edit') ?>
                                </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$users): ?>
                        <tr>
                            <td colspan="8" class="table-empty">
                        Belum ada pengguna yang sesuai dengan pencarian.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <span>
            Menampilkan
                    <?= count($users) ?>
            dari
                    <?= number_format($userStats['total'], 0, ',', '.') ?>
            user
                </span>
                <span class="pagination-note">
            Akun baru dari halaman Register otomatis masuk ke database.
                </span>
            </div>
        </section>
        <dialog class="user-detail-dialog" id="userDetailDialog">
            <div class="dialog-heading">
                <div>
                    <span class="user-table-avatar">
                        <?= ui_icon('user') ?>
                    </span>
                    <h2>
                Detail Pengguna
                    </h2>
                </div>
                <button type="button" data-dialog-close aria-label="Tutup">
            ×
                </button>
            </div>
            <dl>
                <div>
                    <dt>
                Nama
                    </dt>
                    <dd data-detail-name>
                -
                    </dd>
                </div>
                <div>
                    <dt>
                Email
                    </dt>
                    <dd data-detail-email>
                -
                    </dd>
                </div>
                <div>
                    <dt>
                Nomor Telepon
                    </dt>
                    <dd data-detail-phone>
                -
                    </dd>
                </div>
                <div>
                    <dt>
                Tanggal Daftar
                    </dt>
                    <dd data-detail-created>
                -
                    </dd>
                </div>
                <div>
                    <dt>
                Total Pesanan
                    </dt>
                    <dd data-detail-orders>
                0
                    </dd>
                </div>
            </dl>
        </dialog>
