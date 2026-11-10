<section class="container account-layout">
    <?php $accountActive = 'favorites'; include dirname(__DIR__) . '/bagian/menu-akun.php'; ?>
    <div class="account-main">
        <div class="section-heading">
            <div>
                <h1>
                    ♡ Buku Favorit Saya
                </h1>
                <p>
                    Koleksi buku yang Anda simpan untuk dibeli nanti.
                </p>
            </div>
            <?php if($favoriteBooks): ?>
                <form method="post" onsubmit="return confirm('Hapus semua buku favorit?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="favorites_delete_all">
                    <input type="hidden" name="_back" value="<?= e(url('favorites')) ?>">
                    <button class="button button-outline">
                    Hapus Semua
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <?php if($favoriteBooks): ?>
                <div class="book-grid redesigned-book-grid favorite-grid">
                    <?php foreach($favoriteBooks as $book): ?>
                        <article class="book-card redesigned-book-card">
                            <form
                                method="post"
                                action="<?= e(url('favorites')) ?>"
                                class="favorite-remove"
                            >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="favorite_toggle">
                                <input type="hidden" name="_back" value="<?= e(url('favorites')) ?>">
                                <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
                                <button title="Hapus dari favorit">
                        ♥
                                </button>
                            </form>
                            <a class="book-cover-wrap" href="<?= e(url('book',['id'=>$book['id']])) ?>">
                                <img
                                    class="book-cover"
                                    src="<?= e(cover_url($book['cover'])) ?>"
                                    alt="<?= e($book['title']) ?>"
                                >
                            </a>
                            <div class="book-card-body">
                                <a class="book-title" href="<?= e(url('book',['id'=>$book['id']])) ?>">
                                <?= e($book['title']) ?>
                                </a>
                                <span class="book-author">
                                <?= e($book['author']) ?>
                                </span>
                                <div class="book-card-bottom">
                                <b>
                                <?= money($book['price']) ?>
                                </b>
                                <form
                                    class="js-add-to-cart"
                                    method="post"
                                    action="<?= e(url('home')) ?>"
                                    data-cart-endpoint="<?= e(url('home')) ?>"
                                >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="_back" value="<?= e(url('favorites')) ?>">
                                <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
                                <button class="round-cart">
                                🛒
                                </button>
                                </form>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <h2>
                Belum ada buku favorit
                        </h2>
                        <p>
                Simpan buku dengan ikon hati untuk menemukannya kembali di sini.
                        </p>
                        <a class="button button-primary" href="<?= e(url('books')) ?>">
                Jelajahi Buku
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
