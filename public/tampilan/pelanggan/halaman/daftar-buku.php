<?php
$q = trim((string) ($_GET['q'] ?? ''));

$categorySlug = trim((string) ($_GET['category'] ?? ''));

$sort = (string) ($_GET['sort'] ?? 'new');


$sql = '
    SELECT
        b.*,
        c.name AS category_name,
        c.slug AS category_slug
    FROM books b
    LEFT JOIN categories c ON c.id = b.category_id
    WHERE b.status = "active"
';

$params = [];


if ($q !== '') {
    $sql .= '
        AND (
            b.title LIKE ?
            OR b.author LIKE ?
            OR b.publisher LIKE ?
            OR c.name LIKE ?
        )
    ';


    $term = '%' . $q . '%';

    array_push($params, $term, $term, $term, $term);

}

if ($categorySlug !== '') {
    $sql .= ' AND c.slug = ?';

    $params[] = $categorySlug;

}

$sql .= match ($sort) {
    'price-low' => ' ORDER BY b.price ASC',
    'price-high' => ' ORDER BY b.price DESC',
    'popular' => '
        ORDER BY (
            SELECT COALESCE(SUM(oi.quantity), 0)
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE oi.book_id = b.id
                AND o.payment_status = "paid"
        ) DESC, b.is_featured DESC, b.created_at DESC
    ',
    default => ' ORDER BY b.created_at DESC',
};


$statement = db()->prepare($sql);

$statement->execute($params);

$books = $statement->fetchAll();


$currentCategory = null;

foreach ($categories as $category) {
    if ($category['slug'] === $categorySlug) {
        $currentCategory = $category;

        break;

    }
}
?>
<section class="container page-section">
    <div class="page-title-row">
        <div>
            <span class="eyebrow">
                JELAJAHI KOLEKSI
            </span>
            <h1>
                <?= $currentCategory ? e($currentCategory['name']) : 'Semua Buku' ?>
            </h1>
            <p>
                Temukan bacaan yang tepat untuk menemani setiap langkahmu.
            </p>
        </div>
        <span class="result-count">
            <?= count($books) ?>
            buku ditemukan
        </span>
    </div>
    <form class="catalog-toolbar" action="index.php" method="get">
        <input type="hidden" name="page" value="books">
        <?php if ($categorySlug !== ''): ?>
            <input type="hidden" name="category" value="<?= e($categorySlug) ?>">
            <?php endif; ?>
            <label class="search-field">
                <span>
                ⌕
                </span>
                <input
                    type="search"
                    name="q"
                    value="<?= e($q) ?>"
                    placeholder="Cari judul buku, penulis, atau kategori..."
                >
            </label>
            <select name="sort" aria-label="Urutkan buku">
                <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>
                Terbaru
                </option>
                <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>
                Terlaris
                </option>
                <option value="price-low" <?= $sort === 'price-low' ? 'selected' : '' ?>>
                Harga terendah
                </option>
                <option value="price-high" <?= $sort === 'price-high' ? 'selected' : '' ?>>
                Harga tertinggi
                </option>
            </select>
            <button class="button button-primary" type="submit">
            Cari Buku
            </button>
        </form>
        <div class="catalog-layout">
            <aside class="filter-panel">
                <h3>
                Kategori
                </h3>
                <a
                    class="filter-link <?= $categorySlug === '' ? 'selected' : '' ?>"
                    href="<?= e(url('books')) ?>"
                >
                Semua kategori
                </a>
                <?php foreach ($categories as $category): ?>
                    <a
                        class="filter-link <?= $categorySlug === $category['slug'] ? 'selected' : '' ?>"
                        href="<?= e(url('books', ['category' => $category['slug'], 'q' => $q])) ?>"
                    >
                        <?= e($category['name']) ?>
                    </a>
                    <?php endforeach; ?>
                </aside>
                <div class="catalog-results">
                    <?php if ($books): ?>
                        <div class="book-grid catalog-book-grid">
                            <?php foreach ($books as $book): ?>
                                <article class="book-card redesigned-book-card">
                                <?php $isFavorite = in_array((int) $book['id'], $favoriteBookIds, true); ?>
                                <form
                                    class="favorite-toggle"
                                    method="post"
                                    action="<?= e(url('books',['q'=>$q,'category'=>$categorySlug])) ?>"
                                >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="favorite_toggle">
                                <input
                                    type="hidden"
                                    name="_back"
                                    value="<?= e(url('books',['q'=>$q,'category'=>$categorySlug])) ?>"
                                >
                                <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
                                <button
                                    class="<?= $isFavorite ? 'is-favorite' : '' ?>"
                                    type="submit"
                                    aria-pressed="<?= $isFavorite ? 'true' : 'false' ?>"
                                    title="<?= $isFavorite ? 'Hapus dari favorit' : 'Simpan ke favorit' ?>"
                                >
                                <?= $isFavorite ? '♥' : '♡' ?>
                                </button>
                                </form>
                                <a
                                    class="book-cover-wrap"
                                    href="<?= e(url('book', ['id' => $book['id']])) ?>"
                                >
                                <img
                                    class="book-cover"
                                    src="<?= e(cover_url($book['cover'])) ?>"
                                    alt="Cover <?= e($book['title']) ?>"
                                >
                                </a>
                                <div class="book-card-body">
                                <a
                                    class="book-title"
                                    href="<?= e(url('book', ['id' => $book['id']])) ?>"
                                >
                                <?= e($book['title']) ?>
                                </a>
                                <span class="book-author">
                                <?= e($book['author']) ?>
                                </span>
                                <span class="book-category-tag">
                                <?= e($book['category_name'] ?? 'Umum') ?>
                                </span>
                                <div class="book-card-bottom">
                                <b>
                                <?= money($book['price']) ?>
                                </b>
                                <form
                                    class="js-add-to-cart"
                                    action="<?= e(url('home')) ?>"
                                    data-cart-endpoint="<?= e(url('home')) ?>"
                                    method="post"
                                >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button
                                    class="round-cart"
                                    type="submit"
                                    aria-label="Tambah ke keranjang"
                                >
                                <?= ui_icon('cart') ?>
                                </button>
                                </form>
                                </div>
                                <small class="stock-note">
                                <?= (int) $book['stock'] > 0
                                        ? 'Stok tersedia: ' . (int) $book['stock']
                                        : 'Stok habis' ?>
                                </small>
                                </div>
                                </article>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <span>
                    ⌕
                                </span>
                                <h3>
                    Buku belum ditemukan
                                </h3>
                                <p>
                    Coba kata kunci atau kategori lainnya.
                                </p>
                                <a class="button button-primary" href="<?= e(url('books')) ?>">
                    Lihat Semua Buku
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
