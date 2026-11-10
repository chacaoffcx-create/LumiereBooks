<?php
$filterLabels = [
    'all' => ['Semua Pesanan',
         'briefcase-business'],
    
    'pending' => ['Menunggu Pembayaran',
         'wallet'],
    
    'processing' => ['Diproses',
         'box'],
    
    'shipped' => ['Dikirim',
         'truck'],
    
    'delivered' => ['Selesai',
         'circle-check'],
    
    'cancelled' => ['Dibatalkan',
         'circle-x'],
    
];

$orderQuery = static function (array $changes = []) use ($orderFilter, $orderSearch, $orderSort): string {
    return e(url('my-orders',
             array_merge(['status' => $orderFilter,
                     'q' => $orderSearch,
                     'sort' => $orderSort], $changes)));

};

?>
<section class="container account-layout orders-page-layout">
    <?php $accountActive = 'my-orders'; include dirname(__DIR__) . '/bagian/menu-akun.php'; ?>
    <main class="account-main orders-main">
        <div class="orders-heading">
            <div>
                <h1>
                    Pesanan Saya
                </h1>
                <p>
                    Lihat dan pantau semua pesanan yang pernah Anda buat di Lumière Books.
                </p>
            </div>
            <a class="text-link" href="<?= e(url('books')) ?>">
                Belanja Buku
                <span aria-hidden="true">
                    →
                </span>
            </a>
        </div>
        <nav class="order-status-tabs" aria-label="Filter status pesanan">
            <?php foreach ($filterLabels as $filterKey => [$filterLabel, $filterIcon]): ?>
                <a
                    href="<?= $orderQuery(['status' => $filterKey]) ?>"
                    class="<?= $orderFilter === $filterKey ? 'active' : '' ?>"
                >
                    <?= ui_icon($filterIcon) ?>
                    <span>
                        <?= e($filterLabel) ?>
                    </span>
                    <small>
                        <?= (int) ($orderCounts[$filterKey] ?? 0) ?>
                    </small>
                </a>
                <?php endforeach; ?>
            </nav>
            <form class="orders-toolbar" method="get" action="index.php">
                <input type="hidden" name="page" value="my-orders">
                <input type="hidden" name="status" value="<?= e($orderFilter) ?>">
                <label class="orders-search">
                    <?= ui_icon('search') ?>
                    <input
                        type="search"
                        name="q"
                        value="<?= e($orderSearch) ?>"
                        placeholder="Cari nomor pesanan atau judul buku..."
                    >
                </label>
                <label class="orders-sort">
                    <span class="sr-only">
                    Urutkan pesanan
                    </span>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="newest" <?= $orderSort === 'newest' ? 'selected' : '' ?>>
                        Urutkan: Terbaru
                        </option>
                        <option value="oldest" <?= $orderSort === 'oldest' ? 'selected' : '' ?>>
                        Urutkan: Terlama
                        </option>
                        <option value="highest" <?= $orderSort === 'highest' ? 'selected' : '' ?>>
                        Total: Tertinggi
                        </option>
                        <option value="lowest" <?= $orderSort === 'lowest' ? 'selected' : '' ?>>
                        Total: Terendah
                        </option>
                    </select>
                </label>
                <button class="orders-search-submit" type="submit">
                Cari
                </button>
            </form>
            <?php if (!$orders): ?>
                <div class="empty-state orders-empty">
                    <span>
                        <?= ui_icon('package-open') ?>
                    </span>
                    <h2>
                        <?= $orderSearch !== '' || $orderFilter !== 'all' ? 'Pesanan tidak ditemukan' : 'Belum ada pesanan' ?>
                    </h2>
                    <p>
                        <?=$orderSearch !== '' || $orderFilter !== 'all' ?
                            'Coba ubah kata pencarian atau pilih status lainnya.' :
                            'Pesanan yang kamu buat akan tampil di halaman ini.'?>
                    </p>
                    <a class="button button-primary" href="<?= e(url('books')) ?>">
                Mulai Belanja
                    </a>
                </div>
                <?php else: ?>
                <div class="orders-redesign-list">
                    <?php foreach ($orders as $order): ?>
                        <?php
                    $status = (string) $order['order_status'];

                    $items = $order['items'] ?? [];

                    $quantity = array_sum(array_map(static fn($item) => (int) $item['quantity'], $items));

                    $isPayable = in_array($order['payment_status'], ['pending',
         'unpaid'], true) && $status !== 'cancelled';

                    $statusTone = in_array($status, ['shipped',
         'delivered'], true) ? 'success' : (in_array($status, ['cancelled'], true) ? 'danger' : 'warning');

                    ?>
                        <article class="order-card-redesign">
                            <div class="order-card-heading">
                                <div class="order-code-block">
                                <?= ui_icon('clipboard-list') ?>
                                <div>
                                <b>
                                Pesanan:
                                <?= e($order['order_code']) ?>
                                </b>
                                <span>
                                <?= e(date_id($order['created_at'], true)) ?>
                                </span>
                                </div>
                                </div>
                                <span class="order-state-pill <?= e($statusTone) ?>">
                                <?= ui_icon(match ($status) {
                                    'pending' => 'clock',
                                    'processing' => 'box',
                                    'shipped' => 'truck',
                                    'delivered' => 'circle-check',
                                    'cancelled' => 'circle-x',
                                    default => 'info'
                                }) ?>
                                <?= e(order_status_label($status)) ?>
                                </span>
                                <a
                                    class="order-detail-arrow"
                                    href="<?= e(url('order', ['code' => $order['order_code']])) ?>"
                                    aria-label="Lihat detail pesanan <?= e($order['order_code']) ?>"
                                >
                        ›
                                </a>
                            </div>
                            <div class="order-card-body">
                                <div class="order-products-preview">
                                <div class="order-cover-stack">
                                <?php foreach (array_slice($items, 0, 3) as $item): ?>
                                <?php if (!empty($item['cover'])): ?>
                                <img
                                    src="<?= e(cover_url($item['cover'])) ?>"
                                    alt="Sampul <?= e($item['title']) ?>"
                                >
                                <?php else: ?>
                                <span class="order-cover-placeholder">
                                <?= ui_icon('book-open') ?>
                                </span>
                                <?php endif; ?>
                                <?php endforeach; ?>
                                </div>
                                <div class="order-total-copy">
                                <b>
                                <?= $quantity ?>
                                Buku
                                </b>
                                <strong>
                                Total:
                                <?= money($order['total']) ?>
                                </strong>
                                <?php if (count($items) > 3): ?>
                                <small>
                                +
                                <?= count($items) - 3 ?>
                                judul lainnya
                                </small>
                                <?php endif; ?>
                                </div>
                                </div>
                                <div class="order-progress-copy">
                                <?php if ($isPayable): ?>
                                <?= ui_icon('clock') ?>
                                <div>
                                <b>
                                Menunggu Pembayaran
                                </b>
                                <span>
                                <?= e(payment_method_label($order['payment_method'])) ?>
                                </span>
                                </div>
                                <?php elseif ($status === 'shipped'): ?>
                                <?= ui_icon('truck') ?>
                                <div>
                                <b>
                                <?= e($order['courier'] ?: 'Pesanan dikirim') ?>
                                </b>
                                <span>
                                No. Resi:
                                <?= e($order['tracking_number'] ?: 'Belum tersedia') ?>
                                </span>
                                </div>
                                <?php elseif ($status === 'delivered'): ?>
                                <?= ui_icon('circle-check') ?>
                                <div>
                                <b>
                                Pesanan Selesai
                                </b>
                                <span>
                                <?= e(date_id($order['updated_at'] ?: $order['created_at'], true)) ?>
                                </span>
                                </div>
                                <?php elseif ($status === 'cancelled'): ?>
                                <?= ui_icon('circle-x') ?>
                                <div>
                                <b>
                                Pesanan Dibatalkan
                                </b>
                                <span>
                                <?= e(payment_status_label($order['payment_status'])) ?>
                                </span>
                                </div>
                                <?php else: ?>
                                <?= ui_icon('box') ?>
                                <div>
                                <b>
                                Sedang Diproses
                                </b>
                                <span>
                                Pesanan sedang disiapkan oleh Lumière Books.
                                </span>
                                </div>
                                <?php endif; ?>
                                </div>
                                <div class="order-card-actions">
                                <?php if ($isPayable): ?>
                                <a
                                    class="button button-primary"
                                    href="<?= e(url('payment', ['code' => $order['order_code']])) ?>"
                                >
                                <?= ui_icon('credit-card') ?>
                                <span>
                                Bayar Sekarang
                                </span>
                                </a>
                                <?php elseif ($status === 'shipped'): ?>
                                <a
                                    class="button button-primary"
                                    href="<?= e(url('order', ['code' => $order['order_code']])) ?>"
                                >
                                <?= ui_icon('map-pin') ?>
                                <span>
                                Lacak Pengiriman
                                </span>
                                </a>
                                <?php else: ?>
                                <a
                                    class="button button-primary"
                                    href="<?= e(url('order', ['code' => $order['order_code']])) ?>"
                                >
                                <?= ui_icon('file-text') ?>
                                <span>
                                Lihat Detail
                                </span>
                                </a>
                                <?php endif; ?>
                                </div>
                                </div>
                                </article>
                                <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                </main>
                                </section>
