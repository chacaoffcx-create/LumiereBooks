<section class="admin-stat-grid orders-stat-grid">
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('cart') ?>
        </span>
        <div>
            <small>
                Total Pesanan
            </small>
            <b>
                <?= number_format($orderStats['total'], 0, ',', '.') ?>
            </b>
            <em>
                Semua pesanan
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('shield') ?>
        </span>
        <div>
            <small>
                Pesanan Selesai
            </small>
            <b>
                <?= number_format($orderStats['completed'], 0, ',', '.') ?>
            </b>
            <em>
                Sudah diterima
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon stat-icon-warning">
            <?= ui_icon('clock') ?>
        </span>
        <div>
            <small>
                Pesanan Diproses
            </small>
            <b>
                <?= number_format($orderStats['processing'], 0, ',', '.') ?>
            </b>
            <em>
                Menunggu / dikirim
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon stat-icon-danger">
            <?= ui_icon('box') ?>
        </span>
        <div>
            <small>
                Pesanan Dibatalkan
            </small>
            <b>
                <?= number_format($orderStats['cancelled'], 0, ',', '.') ?>
            </b>
            <em>
                Pesanan dibatalkan
            </em>
        </div>
    </article>
</section>
<section class="orders-manager-layout">
    <div class="admin-panel orders-table-panel">
        <form class="admin-filter redesign-filter orders-filter" method="get" action="admin.php">
            <input type="hidden" name="page" value="orders">
            <label class="filter-search">
                <?= ui_icon('search') ?>
                <input
                    type="search"
                    name="q"
                    value="<?= e($orderSearch) ?>"
                    placeholder="Cari pesanan, nama user, atau nomor resi..."
                >
            </label>
            <select name="status" aria-label="Filter status pesanan">
                <option value="">
                    Semua Status
                </option>
                <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                    <option
                        value="<?= e($status) ?>"
                        <?= $statusFilter === $status ? 'selected' : '' ?>
                    >
                        <?= e(order_status_label($status)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button class="button button-outline button-small" type="submit">
                    <?= ui_icon('filter') ?>
                Filter
                </button>
            </form>
            <div class="table-wrap">
                <table class="data-table redesign-table orders-data-table">
                    <thead>
                        <tr>
                            <th>
                            No
                            </th>
                            <th>
                            No. Pesanan
                            </th>
                            <th>
                            Nama User
                            </th>
                            <th>
                            Tanggal Pesan
                            </th>
                            <th>
                            Total
                            </th>
                            <th>
                            Metode
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
                        <?php foreach ($orders as $index => $order): ?>
                            <tr
                                class="<?= (int) ($orderPreview['id'] ?? 0) === (int) $order['id'] ? 'selected-row' : '' ?>"
                            >
                                <td>
                                <?= $index + 1 ?>
                                </td>
                                <td>
                                <b>
                                <?= e($order['order_code']) ?>
                                </b>
                                </td>
                                <td>
                                <?= e($order['customer_name']) ?>
                                </td>
                                <td>
                                <?= date_id($order['created_at']) ?>
                                </td>
                                <td>
                                <?= money($order['total']) ?>
                                </td>
                                <td>
                                <?= e(payment_method_label($order['payment_method'])) ?>
                                </td>
                                <td>
                                <span
                                    class="status-badge status-<?= e(status_class($order['order_status'])) ?>"
                                >
                                <?= e(order_status_label($order['order_status'])) ?>
                                </span>
                                </td>
                                <td>
                                <div class="table-actions">
                                <a
                                    class="action-button"
                                    href="<?= e(admin_url('orders', ['id' => $order['id']])) ?>"
                                    title="Lihat detail"
                                >
                                <?= ui_icon('eye') ?>
                                </a>
                                <a
                                    class="action-button"
                                    href="<?= e(admin_url('order', ['id' => $order['id']])) ?>"
                                    title="Kelola pesanan"
                                >
                                <?= ui_icon('edit') ?>
                                </a>
                                </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (!$orders): ?>
                                <tr>
                                <td colspan="8" class="table-empty">
                            Belum ada pesanan yang sesuai dengan filter.
                                </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-pagination">
                        <span>
                Menampilkan
                            <?= count($orders) ?>
                dari
                            <?= number_format($orderStats['total'], 0, ',', '.') ?>
                pesanan
                        </span>
                        <span class="pagination-note">
                Pilih ikon edit untuk memperbarui status dan nomor resi.
                        </span>
                    </div>
                </div>
                <aside class="admin-panel order-preview-panel">
                    <?php if ($orderPreview): ?>
                        <div class="panel-heading">
                            <div>
                                <h2>
                    Detail Pesanan
                                </h2>
                                <p>
                                <?= e($orderPreview['order_code']) ?>
                                </p>
                            </div>
                            <span
                                class="status-badge status-<?= e(status_class($orderPreview['order_status'])) ?>"
                            >
                                <?= e(order_status_label($orderPreview['order_status'])) ?>
                            </span>
                        </div>
                        <div class="order-preview-meta">
                            <b>
                                <?= e($orderPreview['customer_name']) ?>
                            </b>
                            <small>
                                <?= e($orderPreview['customer_email']) ?>
                            </small>
                            <small>
                                <?= e($orderPreview['phone']) ?>
                            </small>
                            <small>
                                <?= date_id($orderPreview['created_at'], true) ?>
                            </small>
                        </div>
                        <div class="order-preview-address">
                            <h3>
                                <?= ui_icon('truck') ?>
                Alamat Pengiriman
                            </h3>
                            <p>
                                <?= nl2br(e($orderPreview['shipping_address'])) ?>
                                <br>
                                <?= e($orderPreview['city']) ?>
                ,
                                <?= e($orderPreview['province']) ?>
                                <?= e($orderPreview['postal_code']) ?>
                            </p>
                        </div>
                        <div class="order-preview-address">
                            <h3>
                                <?= ui_icon('wallet') ?>
                Metode Pembayaran
                            </h3>
                            <p>
                                <?= e(payment_method_label(
                        $orderPreview['payment_method'],
                        $orderPreviewPayment['payment_type'] ?? null
                    )) ?>
                            </p>
                            <span
                                class="status-badge status-<?= e(status_class($orderPreview['payment_status'])) ?>"
                            >
                                <?= e(payment_status_label($orderPreview['payment_status'])) ?>
                            </span>
                        </div>
                        <div class="order-preview-items">
                            <h3>
                Daftar Pesanan
                            </h3>
                            <?php foreach ($orderPreviewItems as $item): ?>
                                <div class="order-preview-item">
                                <span class="order-book-placeholder">
                                <?= ui_icon('book') ?>
                                </span>
                                <span>
                                <b>
                                <?= e($item['title']) ?>
                                </b>
                                <small>
                                <?= e($item['author']) ?>
                                </small>
                                </span>
                                <small>
                                <?= money($item['price']) ?>
                    ×
                                <?= (int) $item['quantity'] ?>
                                </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="order-preview-total">
                                <div>
                                <span>
                    Subtotal
                                </span>
                                <b>
                                <?= money($orderPreview['subtotal']) ?>
                                </b>
                                </div>
                                <div>
                                <span>
                    Ongkos Kirim
                                </span>
                                <b>
                                <?= money($orderPreview['shipping_cost']) ?>
                                </b>
                                </div>
                                <div class="grand-total">
                                <span>
                    Total Pembayaran
                                </span>
                                <b>
                                <?= money($orderPreview['total']) ?>
                                </b>
                                </div>
                            </div>
                            <a
                                class="button button-primary button-full"
                                href="<?= e(admin_url('order', ['id' => $orderPreview['id']])) ?>"
                            >
                                <?= ui_icon('file') ?>
            Kelola Detail Pesanan
                            </a>
                            <?php else: ?>
                            <div class="empty-inline order-empty">
                                <span>
                                <?= ui_icon('cart') ?>
                                </span>
                                <b>
                Belum ada detail pesanan
                                </b>
                                <small>
                Detail pesanan akan tampil saat ada transaksi masuk.
                                </small>
                            </div>
                            <?php endif; ?>
                        </aside>
                    </section>
