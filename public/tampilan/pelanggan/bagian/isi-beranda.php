<section class="container section-block home-category-section">
    <div class="section-heading">
        <div>
            <span class="eyebrow">
                PILIH GENRE FAVORITMU
            </span>
            <h2>
                Kategori Buku
                <i>
                </i>
            </h2>
        </div>
        <a class="text-link" href="<?= e(url('categories')) ?>">
            Lihat Semua Kategori
            <?= ui_icon('arrow-right') ?>
        </a>
    </div>
    <?php
    $categoryIcons = [
        'fiksi' => 'book-open',
    
        'non-fiksi' => 'book',
    
        'self-help' => 'sprout',
    
        'bisnis-ekonomi' => 'chart',
    
        'pendidikan' => 'graduation',
    
        'anak-anak' => 'kids',
    
        'remaja' => 'star',
    
        'horor' => 'ghost',
    
        'romance' => 'heart',
    
        'klasik' => 'classical',
    
    ];

    ?>
    <div class="category-grid">
        <?php foreach (array_slice($categories, 0, 10) as $category): ?>
            <?php
            $countStatement = db()->prepare(
                'SELECT COUNT(*)
                 FROM books
                 WHERE category_id = ? AND status = "active"'
            );

            $countStatement->execute([$category['id']]);

            $count = (int) $countStatement->fetchColumn();


            $iconName = $categoryIcons[$category['slug']] ?? 'book-open';

            ?>
            <a
                class="category-card redesigned-category-card"
                href="<?= e(url('books', ['category' => $category['slug']])) ?>"
            >
                <img
                    class="category-card-photo category-card-photo-background"
                    src="<?= e(lumiere_category_photo_url($category)) ?>"
                    alt=""
                    aria-hidden="true"
                    loading="lazy"
                    onerror="this.style.display='none'"
                >
                <span class="category-symbol">
                    <?= ui_icon($iconName) ?>
                </span>
                <b>
                    <?= e($category['name']) ?>
                </b>
                <small>
                    <?= number_format($count, 0, ',', '.') ?>
                Buku
                </small>
                <span class="circle-arrow">
                    <?= ui_icon('arrow-right') ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="container recommendation-section section-block">
        <div class="recommendation-inner">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">
                    BUKU PILIHAN
                    </span>
                    <h2>
                    Rekomendasi Untukmu
                        <i>
                        </i>
                    </h2>
                </div>
                <a class="text-link" href="<?= e(url('books')) ?>">
                Lihat Semua Buku
                    <?= ui_icon('arrow-right') ?>
                </a>
            </div>
            <div class="book-grid redesigned-book-grid">
                <?php foreach (array_slice($featuredBooks, 0, 6) as $book): ?>
                    <article class="book-card redesigned-book-card">
                        <?php $isFavorite = in_array((int) $book['id'], $favoriteBookIds, true); ?>
                        <?php if ($user): ?>
                        <form class="favorite-toggle" method="post" action="<?= e(url('home')) ?>">
                        <?php else: ?>
                        <form class="favorite-toggle" method="get" action="index.php">
                            <input type="hidden" name="page" value="login">
                            <input type="hidden" name="from" value="favorit">
                        <?php endif; ?>
                            <?php if ($user): ?>
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="favorite_toggle">
                            <input type="hidden" name="_back" value="<?= e(url('home')) ?>">
                            <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
                            <?php endif; ?>
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
                                src="<?= e(cover_url($book['cover'])) ?>"
                                alt="Cover <?= e($book['title']) ?>"
                                class="book-cover"
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
                            <span class="book-rating">
                                <span>
                            ★★★★★
                                </span>
                                <small>
                            Buku pilihan Lumière Books
                                </small>
                            </span>
                            <div class="book-card-bottom">
                                <b>
                                <?= money($book['price']) ?>
                                </b>
                                <?php if ($user): ?>
                                <form
                                    class="js-add-to-cart"
                                    action="<?= e(url('home')) ?>"
                                    data-cart-endpoint="<?= e(url('home')) ?>"
                                    method="post"
                                >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="add_to_cart">
                                <input
                                    type="hidden"
                                    name="book_id"
                                    value="<?= (int) $book['id'] ?>"
                                >
                                <input type="hidden" name="quantity" value="1">
                                <?php else: ?>
                                <form action="index.php" method="get">
                                <input type="hidden" name="page" value="login">
                                <input type="hidden" name="from" value="keranjang">
                                <?php endif; ?>
                                <button
                                    class="round-cart"
                                    type="submit"
                                    aria-label="Tambah <?= e($book['title']) ?> ke keranjang"
                                >
                                <?= ui_icon('cart') ?>
                                </button>
                                </form>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <section class="container promo-grid section-block">
            <article class="promo-card redesigned-promo promo-sale">
                <div class="promo-copy">
                    <span class="eyebrow">
                DISKON SPESIAL
                    </span>
                    <h2>
                Up to 30%
                    </h2>
                    <p>
                Untuk Buku Pilihan
                    </p>
                    <a class="button button-primary" href="<?= e(url('books')) ?>">
                Lihat Koleksi
                        <?= ui_icon('arrow-right') ?>
                    </a>
                </div>
                <img
                    src="<?= e(asset('images/promo-sale.jpg')) ?>"
                    alt="Promo diskon buku pilihan dengan tumpukan buku dan vas bunga"
                >
            </article>
            <article class="promo-card redesigned-promo promo-new">
                <div class="promo-copy">
                    <span class="eyebrow">
                BUKU BARU
                    </span>
                    <h2>
                Rilis Terbaru
                    </h2>
                    <p>
                Jangan sampai ketinggalan koleksi terbaru kami!
                    </p>
                    <a
                        class="button button-primary"
                        href="<?= e(url('books', ['sort' => 'new'])) ?>"
                    >
                Lihat Sekarang
                        <?= ui_icon('arrow-right') ?>
                    </a>
                </div>
                <img
                    src="<?= e(asset('images/promo-new.jpg')) ?>"
                    alt="Koleksi buku terbaru dengan lampu hangat dan tumpukan buku"
                >
            </article>
        </section>
        <section class="container home-benefits">
            <div>
                <?= ui_icon('book-open') ?>
                <span>
                    <b>
                Buku Original
                    </b>
                    <small>
                100%
                    </small>
                </span>
            </div>
            <div>
                <?= ui_icon('shield') ?>
                <span>
                    <b>
                Pembayaran Aman
                    </b>
                    <small>
                (COD / Transfer)
                    </small>
                </span>
            </div>
            <div>
                <?= ui_icon('truck') ?>
                <span>
                    <b>
                Pengiriman Cepat
                    </b>
                    <small>
                Seluruh Indonesia
                    </small>
                </span>
            </div>
            <div>
                <?= ui_icon('headset') ?>
                <span>
                    <b>
                Layanan Pelanggan
                    </b>
                    <small>
                Siap membantu kamu
                    </small>
                </span>
            </div>
        </section>
