<?php
$bookId = (int) ($_GET['id'] ?? 0);

$statement = db()->prepare(
'SELECT b.*, c.name AS category_name FROM books b
LEFT JOIN categories c ON c.id = b.category_id
WHERE b.id = ? AND b.status = "active" LIMIT 1'
);

$statement->execute([$bookId]);

$book = $statement->fetch();

if (!$book): ?>
    <section class="container page-section">
        <div class="empty-state">
            <h2>
            Buku tidak ditemukan
            </h2>
            <a class="button button-primary" href="<?= e(url('books')) ?>">
            Kembali ke katalog
            </a>
        </div>
    </section>
    <?php else: ?>
    <section class="container page-section">
        <div class="breadcrumb">
            <a href="<?= e(url('home')) ?>">
            Beranda
            </a>
            <span>
            /
            </span>
            <a href="<?= e(url('books')) ?>">
            Buku
            </a>
            <span>
            /
            </span>
            <b>
                <?= e($book['title']) ?>
            </b>
        </div>
        <div class="book-detail-card">
            <div class="book-detail-cover">
                <img src="<?= e(cover_url($book['cover'])) ?>" alt="Cover <?= e($book['title']) ?>">
            </div>
            <div class="book-detail-info">
                <span class="book-category-tag">
                    <?= e($book['category_name'] ?? 'Umum') ?>
                </span>
                <h1>
                    <?= e($book['title']) ?>
                </h1>
                <p class="detail-author">
                Oleh
                    <b>
                        <?= e($book['author']) ?>
                    </b>
                </p>
                <div class="detail-rating">
                ★★★★★
                    <span>
                    Rekomendasi pembaca Lumière Books
                    </span>
                </div>
                <h2 class="detail-price">
                    <?= money($book['price']) ?>
                </h2>
                <p class="detail-stock">
                    <?= (int) $book['stock'] > 0 ? '✓ Tersedia · ' . (int) $book['stock'] . ' stok' : 'Stok sedang habis' ?>
                </p>
                <p class="detail-description">
                    <?= nl2br(e($book['description'] ?: 'Belum ada deskripsi untuk buku ini.')) ?>
                </p>
                <?php $isFavorite = in_array((int) $book['id'], $favoriteBookIds, true); ?>
                <form
                    class="favorite-detail-form"
                    method="post"
                    action="<?= e(url('book', ['id' => $book['id']])) ?>"
                >
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="favorite_toggle">
                    <input
                        type="hidden"
                        name="_back"
                        value="<?= e(url('book', ['id' => $book['id']])) ?>"
                    >
                    <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                    <button
                        class="button button-outline favorite-detail-button <?= $isFavorite ? 'is-favorite' : '' ?>"
                        type="submit"
                        aria-pressed="<?= $isFavorite ? 'true' : 'false' ?>"
                    >
                        <?= $isFavorite ? '♥ Sudah di Favorit' : '♡ Simpan ke Favorit' ?>
                    </button>
                </form>
                <div class="book-meta">
                    <span>
                    SKU
                        <b>
                            <?= e($book['sku']) ?>
                        </b>
                    </span>
                    <?php if ($book['publisher']): ?>
                        <span>
                    Penerbit
                            <b>
                                <?= e($book['publisher']) ?>
                            </b>
                        </span>
                        <?php endif; ?>
                        <?php if ($book['isbn']): ?>
                            <span>
                    ISBN
                                <b>
                                <?= e($book['isbn']) ?>
                                </b>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php if ((int) $book['stock'] > 0): ?>
                            <form
                                class="add-cart-form js-add-to-cart"
                                action="<?= e(url('home')) ?>"
                                data-cart-endpoint="<?= e(url('home')) ?>"
                                method="post"
                            >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                <label>
                    Jumlah
                                <input
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="<?= (int) $book['stock'] ?>"
                                    value="1"
                                >
                                </label>
                                <button class="button button-primary" type="submit">
                    🛒 Tambah ke Keranjang
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
