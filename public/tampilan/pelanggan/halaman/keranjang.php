<section class="container page-section">
    <div class="page-title-row">
        <div>
            <span class="eyebrow">
                BELANJA PILIHANMU
            </span>
            <h1>
                Keranjang Belanja
            </h1>
            <p>
                Periksa kembali buku yang ingin kamu pesan.
            </p>
        </div>
        <a class="text-link" href="<?= e(url('books')) ?>">
            ← Lanjut Belanja
        </a>
    </div>
    <?php if (!$cartItems): ?>
        <div class="empty-state">
            <span>
            🛒
            </span>
            <h2>
            Keranjangmu masih kosong
            </h2>
            <p>
            Yuk, temukan buku menarik untuk dibaca.
            </p>
            <a class="button button-primary" href="<?= e(url('books')) ?>">
            Jelajahi Buku
            </a>
        </div>
        <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items-card">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <img
                            src="<?= e(cover_url($item['cover'])) ?>"
                            alt="Cover <?= e($item['title']) ?>"
                        >
                        <div class="cart-item-info">
                            <a href="<?= e(url('book', ['id' => $item['book_id']])) ?>">
                                <b>
                                <?= e($item['title']) ?>
                                </b>
                            </a>
                            <small>
                                <?= e($item['author']) ?>
                            </small>
                            <strong>
                                <?= money($item['price']) ?>
                            </strong>
                            <small>
                        Stok tersedia:
                                <?= (int) $item['stock'] ?>
                            </small>
                        </div>
                        <form
                            class="quantity-form"
                            action="<?= e(url('cart')) ?>"
                            method="post"
                        >
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="update_cart">
                            <input
                                type="hidden"
                                name="cart_id"
                                value="<?= (int) $item['cart_id'] ?>"
                            >
                            <label
                                class="sr-only"
                                for="qty-<?= (int) $item['cart_id'] ?>"
                            >
                        Jumlah
                            </label>
                            <input
                                id="qty-<?= (int) $item['cart_id'] ?>"
                                type="number"
                                name="quantity"
                                min="0"
                                max="<?= (int) $item['stock'] ?>"
                                value="<?= (int) $item['quantity'] ?>"
                            >
                            <button
                                type="submit"
                                class="button button-outline button-small"
                            >
                        Perbarui
                            </button>
                        </form>
                        <div class="cart-line-total">
                            <b>
                                <?= money((float) $item['price'] * (int) $item['quantity']) ?>
                            </b>
                            <form
                                method="post"
                                action="<?= e(url('cart')) ?>"
                                data-confirm="Hapus buku ini dari keranjang?"
                            >
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="remove_cart">
                                <input
                                    type="hidden"
                                    name="cart_id"
                                    value="<?= (int) $item['cart_id'] ?>"
                                >
                                <button class="remove-link" type="submit">
                            Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <aside class="summary-card">
                    <h2>
                Ringkasan Belanja
                    </h2>
                    <div>
                        <span>
                    Subtotal
                        </span>
                        <b>
                            <?= money($cartSubtotal) ?>
                        </b>
                    </div>
                    <div>
                        <span>
                    Estimasi pengiriman
                        </span>
                        <b>
                            <?= money(15000) ?>
                        </b>
                    </div>
                    <div class="summary-total">
                        <span>
                    Total
                        </span>
                        <b>
                            <?= money($cartSubtotal + 15000) ?>
                        </b>
                    </div>
                    <a class="button button-primary button-full" href="<?= e(url('checkout')) ?>">
                Lanjut ke Checkout →
                    </a>
                    <small>
                Pajak dan biaya pengiriman dapat disesuaikan pada tahap checkout.
                    </small>
                </aside>
            </div>
            <?php endif; ?>
        </section>
