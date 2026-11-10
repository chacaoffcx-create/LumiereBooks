<section class="admin-stat-grid books-stat-grid">
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('book-open') ?>
        </span>
        <div>
            <small>
                Total Buku
            </small>
            <b>
                <?= number_format($bookMetrics['total'], 0, ',', '.') ?>
            </b>
            <em>
                Koleksi Lumière Books
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('grid') ?>
        </span>
        <div>
            <small>
                Total Kategori
            </small>
            <b>
                <?= number_format($bookMetrics['categories'], 0, ',', '.') ?>
            </b>
            <em>
                Genre buku
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon stat-icon-warning">
            <?= ui_icon('box') ?>
        </span>
        <div>
            <small>
                Stok Menipis
            </small>
            <b>
                <?= number_format($bookMetrics['low_stock'], 0, ',', '.') ?>
            </b>
            <em>
                Perlu diperhatikan
            </em>
        </div>
    </article>
    <a
        class="button button-primary add-book-top"
        href="<?= e(admin_url('books', ['form' => 1])) ?>#book-form"
    >
        <?= ui_icon('plus') ?>
        Tambah Buku
    </a>
</section>
<section class="admin-content-grid books-admin-grid <?= $showBookForm ? 'has-book-form' : '' ?>">
    <section class="admin-panel admin-table-panel">
        <form class="admin-filter redesign-filter" method="get" action="admin.php">
            <input type="hidden" name="page" value="books">
            <label class="filter-search">
                <?= ui_icon('search') ?>
                <input
                    type="search"
                    name="q"
                    value="<?= e($search) ?>"
                    placeholder="Cari buku..."
                >
            </label>
            <select name="category" aria-label="Filter kategori">
                <option value="0">
                    Semua Kategori
                </option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= (int) $category['id'] ?>"
                        <?= $categoryFilter === (int) $category['id'] ? 'selected' : '' ?>
                    >
                        <?= e($category['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <select name="status" aria-label="Filter status">
                    <option value="">
                    Semua Status
                    </option>
                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>
                    Tersedia
                    </option>
                    <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>
                    Nonaktif
                    </option>
                </select>
                <button class="button button-outline button-small" type="submit">
                    <?= ui_icon('filter') ?>
                Filter
                </button>
                <a
                    class="button button-outline button-small"
                    href="<?= e(admin_url('books')) ?>"
                >
                Reset
                </a>
            </form>
            <div class="table-wrap">
                <table class="data-table redesign-table">
                    <thead>
                        <tr>
                            <th>
                            No
                            </th>
                            <th>
                            Cover
                            </th>
                            <th>
                            Judul Buku
                            </th>
                            <th>
                            Penulis
                            </th>
                            <th>
                            Kategori
                            </th>
                            <th>
                            Harga
                            </th>
                            <th>
                            Stok
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
                        <?php foreach ($books as $index => $book): ?>
                            <tr>
                                <td>
                                <?= $index + 1 ?>
                                </td>
                                <td>
                                <img
                                    class="table-cover"
                                    src="<?= e(cover_url($book['cover'])) ?>"
                                    alt="Cover <?= e($book['title']) ?>"
                                >
                                </td>
                                <td class="book-table-title">
                                <b>
                                <?= e($book['title']) ?>
                                </b>
                                <small>
                                #
                                <?= e($book['sku']) ?>
                                </small>
                                </td>
                                <td>
                                <?= e($book['author']) ?>
                                </td>
                                <td>
                                <span class="category-pill">
                                <?= e($book['category_name'] ?? 'Tanpa Kategori') ?>
                                </span>
                                </td>
                                <td>
                                <?= money($book['price']) ?>
                                </td>
                                <td>
                                <?= number_format((int) $book['stock'], 0, ',', '.') ?>
                                </td>
                                <td>
                                <div class="book-status-cell">
                                <span
                                    class="status-badge status-<?= e(status_class($book['status'])) ?>"
                                >
                                <?= e($book['status'] === 'active' ? 'Tersedia' : 'Nonaktif') ?>
                                </span>
                                <form
                                    method="post"
                                    action="<?= e(admin_url('books')) ?>"
                                    data-confirm="<?= e(
                                        $book['status'] === 'active'
                                            ? 'Nonaktifkan buku ini dari katalog pelanggan?'
                                            : 'Aktifkan kembali buku ini di katalog pelanggan?'
                                    ) ?>"
                                >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="toggle_book_status">
                                <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                <input
                                    type="hidden"
                                    name="new_status"
                                    value="<?= e($book['status'] === 'active' ? 'inactive' : 'active') ?>"
                                >
                                <button class="book-status-toggle" type="submit">
                                <?= e($book['status'] === 'active' ? 'Jadikan Nonaktif' : 'Aktifkan') ?>
                                </button>
                                </form>
                                </div>
                                </td>
                                <td>
                                <div class="table-actions">
                                <a
                                    class="action-button"
                                    href="<?= e(url('book', ['id' => $book['id']])) ?>"
                                    target="_blank"
                                    title="Lihat buku"
                                >
                                <?= ui_icon('eye') ?>
                                </a>
                                <a
                                    class="action-button"
                                    href="<?= e(admin_url('books', ['edit' => $book['id']])) ?>#book-form"
                                    title="Edit buku"
                                >
                                <?= ui_icon('edit') ?>
                                </a>
                                <form
                                    method="post"
                                    action="<?= e(admin_url('books')) ?>"
                                    data-confirm="Yakin ingin menghapus buku ini?"
                                >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete_book">
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $book['id'] ?>"
                                >
                                <button
                                    class="action-button danger"
                                    type="submit"
                                    title="Hapus buku"
                                >
                                <?= ui_icon('trash') ?>
                                </button>
                                </form>
                                </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (!$books): ?>
                                <tr>
                                <td colspan="9" class="table-empty">
                            Tidak ada buku yang sesuai dengan pencarian.
                                </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-pagination">
                        <span>
                Menampilkan
                            <?= count($books) ?>
                dari
                            <?= number_format($bookMetrics['total'], 0, ',', '.') ?>
                buku
                        </span>
                        <span class="pagination-note">
                Data tersimpan langsung ke database MySQL.
                        </span>
                    </div>
                </section>
                <?php if ($showBookForm): ?>
                    <aside class="admin-panel admin-form-panel book-form-panel" id="book-form">
                        <div class="panel-heading">
                            <div>
                                <h2>
                                <?= $editBook ? 'Edit Buku' : 'Tambah Buku Baru' ?>
                                </h2>
                                <p>
                    Isi informasi buku dan unggah cover.
                                </p>
                            </div>
                            <a
                                class="panel-close"
                                href="<?= e(admin_url('books')) ?>"
                                aria-label="Tutup formulir"
                            >
                ×
                            </a>
                        </div>
                        <form
                            method="post"
                            action="<?= e(admin_url('books')) ?>"
                            enctype="multipart/form-data"
                        >
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="save_book">
                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) ($editBook['id'] ?? 0) ?>"
                            >
                            <input
                                type="hidden"
                                name="_back"
                                value="<?=e($editBook ? admin_url('books', ['edit' => $editBook['id']]) . '#book-form' :
                                    admin_url('books', ['form' => 1]) . '#book-form')?>"
                            >
                            <label>
                Judul Buku *
                                <input
                                    type="text"
                                    name="title"
                                    required
                                    value="<?= e($editBook['title'] ?? '') ?>"
                                    placeholder="Masukkan judul buku..."
                                >
                            </label>
                            <label>
                Penulis *
                                <input
                                    type="text"
                                    name="author"
                                    required
                                    value="<?= e($editBook['author'] ?? '') ?>"
                                    placeholder="Masukkan nama penulis..."
                                >
                            </label>
                            <label>
                Kategori *
                                <select name="category_id" required>
                                <option value="">
                        Pilih kategori
                                </option>
                                <?php foreach ($categories as $category): ?>
                                <option
                                    value="<?= (int) $category['id'] ?>"
                                    <?= (int) ($editBook['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>
                                >
                                <?= e($category['name']) ?>
                                </option>
                                <?php endforeach; ?>
                                </select>
                                </label>
                                <div class="form-grid-two">
                                <label>
                    Harga *
                                <span class="input-prefix">
                                <b>
                            Rp
                                </b>
                                <input
                                    type="number"
                                    name="price"
                                    min="1"
                                    step="1"
                                    required
                                    value="<?= e($editBook['price'] ?? '') ?>"
                                    placeholder="95000"
                                >
                                </span>
                                </label>
                                <label>
                    Stok *
                                <input
                                    type="number"
                                    name="stock"
                                    min="0"
                                    required
                                    value="<?= e($editBook['stock'] ?? '0') ?>"
                                    placeholder="Jumlah stok"
                                >
                                </label>
                                </div>
                                <label>
                Penerbit
                                <input
                                    type="text"
                                    name="publisher"
                                    value="<?= e($editBook['publisher'] ?? '') ?>"
                                    placeholder="Nama penerbit"
                                >
                                </label>
                                <label>
                ISBN
                                <input
                                    type="text"
                                    name="isbn"
                                    value="<?= e($editBook['isbn'] ?? '') ?>"
                                    placeholder="Nomor ISBN"
                                >
                                </label>
                                <label>
                Deskripsi *
                                <textarea
                                    name="description"
                                    rows="4"
                                    required
                                    placeholder="Masukkan deskripsi buku..."
                                >
                                <?= e($editBook['description'] ?? '') ?>
                    </textarea>
                                </label>
                                <label class="upload-label">
                                <?= $editBook ? 'Ganti Cover (opsional)' : 'Upload Cover *' ?>
                                <span class="upload-dropzone">
                                <input
                                    type="file"
                                    name="cover"
                                    accept="image/jpeg,image/png,image/webp"
                                    data-image-preview
                                    <?= $editBook ? '' : 'required' ?>
                                >
                                <span class="upload-preview">
                                <?php if (!empty($editBook['cover'])): ?>
                                <img
                                    src="<?= e(cover_url($editBook['cover'])) ?>"
                                    alt="Cover buku saat ini"
                                >
                                <?php else: ?>
                                <?= ui_icon('image') ?>
                                <?php endif; ?>
                                </span>
                                <b>
                            Pilih Gambar
                                </b>
                                <small>
                                <?= $editBook
                    ? 'Pilih gambar baru jika ingin mengganti cover. Kosongkan untuk mempertahankan cover saat ini.'
                    : 'Format: JPG, PNG, WEBP (Maks. 2 MB)' ?>
                                </small>
                                </span>
                                </label>
                                <div class="form-action-row">
                                <a class="button button-outline" href="<?= e(admin_url('books')) ?>">
                        Batal
                                </a>
                                <button class="button button-primary" type="submit">
                        Simpan Buku
                                </button>
                                </div>
                                </form>
                                </aside>
                                <?php endif; ?>
                            </section>
