<section class="category-manager-layout">
    <div class="category-manager-main">
        <div class="admin-toolbar-row">
            <form class="admin-search-filter" method="get" action="admin.php">
                <input type="hidden" name="page" value="categories">
                <span class="category-search-icon" aria-hidden="true">
                    <?= ui_icon('search') ?>
                </span>
                <input
                    type="search"
                    name="q"
                    placeholder="Cari kategori..."
                    value="<?= e($_GET['q'] ?? '') ?>"
                >
            </form>
            <a
                class="button button-primary"
                href="<?= e(admin_url('categories', ['form' => 1])) ?>#category-form"
            >
                <span class="category-add-icon" aria-hidden="true">
                    <?= ui_icon('plus') ?>
                </span>
                <span>
                    Tambah Kategori
                </span>
            </a>
        </div>
        <div class="admin-category-grid redesign-category-grid">
            <?php foreach ($categories as $category): ?>
                <article class="admin-category-card redesign-category-card">
                    <?php
                        $hasCategoryImage = !empty($category['image']);

                        $categoryImageUrl = $hasCategoryImage
                            ? category_image_url($category)
                            : '';

                    ?>
                    <a
                        class="category-admin-art <?= $hasCategoryImage ? 'has-category-image' : 'category-art-placeholder' ?>"
                        href="<?= e(admin_url('books', ['category' => $category['id']])) ?>"
                    >
                        <?php if ($hasCategoryImage && $categoryImageUrl !== ''): ?>
                            <img
                                class="category-card-photo"
                                src="<?= e($categoryImageUrl) ?>"
                                alt=""
                                aria-hidden="true"
                                onerror="this.style.display='none'"
                            >
                            <?php endif; ?>
                            <span class="category-admin-icon">
                                <?= ui_icon('book-open') ?>
                            </span>
                            <b>
                                <?= e($category['name']) ?>
                            </b>
                            <small>
                                <?= number_format((int) $category['book_count'], 0, ',', '.') ?>
                        Buku
                            </small>
                        </a>
                        <div class="category-card-actions">
                            <a
                                class="action-button"
                                href="<?= e(admin_url('categories', ['edit' => $category['id']])) ?>"
                                title="Edit kategori"
                            >
                                <?= ui_icon('edit') ?>
                            </a>
                            <form
                                method="post"
                                action="<?= e(admin_url('categories')) ?>"
                                data-confirm="Hapus kategori ini? Buku terkait tidak ikut dihapus."
                            >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete_category">
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $category['id'] ?>"
                                >
                                <button
                                    class="action-button danger"
                                    type="submit"
                                    title="Hapus kategori"
                                >
                                <?= ui_icon('trash') ?>
                                </button>
                            </form>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    <?php if (!$categories): ?>
                        <div class="empty-inline">
                            <b>
                    Belum ada kategori buku.
                            </b>
                            <small>
                    Tambahkan kategori pertama melalui formulir di samping.
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <aside class="admin-panel admin-form-panel category-form-panel" id="category-form">
                    <div class="panel-heading">
                        <div>
                            <h2>
                                <?= $editCategory ? 'Edit Kategori' : 'Tambah Kategori' ?>
                            </h2>
                            <p>
                    Lengkapi informasi kategori buku.
                            </p>
                        </div>
                        <?php if ($editCategory): ?>
                            <a
                                class="panel-close"
                                href="<?= e(admin_url('categories')) ?>"
                                aria-label="Tutup formulir"
                            >
                ×
                            </a>
                            <?php endif; ?>
                        </div>
                        <form
                            method="post"
                            action="<?= e(admin_url('categories')) ?>"
                            enctype="multipart/form-data"
                        >
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="save_category">
                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) ($editCategory['id'] ?? 0) ?>"
                            >
                            <label>
                Nama Kategori *
                                <input
                                    type="text"
                                    name="name"
                                    required
                                    value="<?= e($editCategory['name'] ?? '') ?>"
                                    placeholder="Masukkan nama kategori..."
                                >
                            </label>
                            <label>
                Deskripsi
                                <textarea
                                    name="description"
                                    rows="4"
                                    placeholder="Masukkan deskripsi kategori..."
                                >
                                <?= e($editCategory['description'] ?? '') ?>
                    </textarea>
                                </label>
                                <label class="upload-label">
                    Upload Gambar
                                <span class="upload-dropzone">
                                <input
                                    type="file"
                                    name="image"
                                    accept="image/jpeg,image/png,image/webp"
                                    data-image-preview
                                >
                                <span class="upload-preview">
                                <?php if (!empty($editCategory['image'])): ?>
                                <img
                                    src="<?= e(category_image_url($editCategory)) ?>"
                                    alt="Gambar kategori saat ini"
                                >
                                <?php else: ?>
                                <?= ui_icon('image') ?>
                                <?php endif; ?>
                                </span>
                                <b>
                            Pilih Gambar
                                </b>
                                <small>
                            Format: JPG, PNG (Maks. 2 MB)
                                </small>
                                </span>
                                </label>
                                <div class="form-action-row">
                                <a
                                    class="button button-outline"
                                    href="<?= e(admin_url('categories')) ?>"
                                >
                        Batal
                                </a>
                                <button class="button button-primary" type="submit">
                        Simpan
                                </button>
                                </div>
                                </form>
                            </aside>
                        </section>
