<?php if (empty($order)): ?>
    <section class="admin-panel">
        <h2>
        Pesanan tidak ditemukan
        </h2>
        <a href="<?= e(admin_url('orders')) ?>">
        Kembali
        </a>
    </section>
    <?php else: ?>
    <div class="admin-content-grid order-admin-grid">
        <section class="admin-panel">
            <div class="panel-heading">
                <h2>
                    <?= e($order['order_code']) ?>
                </h2>
                <a href="<?= e(admin_url('orders')) ?>">
                ← Kembali
                </a>
            </div>
            <div class="order-detail-meta">
                <div>
                    <small>
                    Pemesan
                    </small>
                    <b>
                        <?= e($order['customer_name']) ?>
                    </b>
                    <span>
                        <?= e($order['customer_email']) ?>
                    </span>
                </div>
                <div>
                    <small>
                    Tanggal Pesanan
                    </small>
                    <b>
                        <?= date_id($order['created_at'], true) ?>
                    </b>
                </div>
                <div>
                    <small>
                    Total Pembayaran
                    </small>
                    <b>
                        <?= money($order['total']) ?>
                    </b>
                </div>
            </div>
            <h3 class="subheading">
            Daftar Produk
            </h3>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>
                            Produk
                            </th>
                            <th>
                            Harga
                            </th>
                            <th>
                            Jumlah
                            </th>
                            <th>
                            Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                <b>
                                <?= e($item['title']) ?>
                                </b>
                                <small>
                                <?= e($item['author']) ?>
                                </small>
                                </td>
                                <td>
                                <?= money($item['price']) ?>
                                </td>
                                <td>
                                <?= (int) $item['quantity'] ?>
                                </td>
                                <td>
                                <?= money($item['subtotal']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="admin-total-lines">
                    <div>
                        <span>
                    Subtotal
                        </span>
                        <b>
                            <?= money($order['subtotal']) ?>
                        </b>
                    </div>
                    <div>
                        <span>
                    Pengiriman
                        </span>
                        <b>
                            <?= money($order['shipping_cost']) ?>
                        </b>
                    </div>
                    <div>
                        <span>
                    Total
                        </span>
                        <b>
                            <?= money($order['total']) ?>
                        </b>
                    </div>
                </div>
                <h3 class="subheading">
            Alamat Pengiriman
                </h3>
                <p>
                    <?= e($order['recipient_name']) ?>
            ·
                    <?= e($order['phone']) ?>
                    <br>
                    <?= nl2br(e($order['shipping_address'])) ?>
                    <br>
                    <?= e($order['city']) ?>
            ,
                    <?= e($order['province']) ?>
                    <?= e($order['postal_code']) ?>
                </p>
            </section>
            <section class="admin-panel admin-form-panel">
                <div class="panel-heading">
                    <h2>
                Perbarui Pesanan
                    </h2>
                </div>
                <div class="current-status">
                    <span class="status-badge status-<?= e(status_class($order['payment_status'])) ?>">
                        <?= e(payment_status_label($order['payment_status'])) ?>
                    </span>
                    <span class="status-badge status-<?= e(status_class($order['order_status'])) ?>">
                        <?= e(order_status_label($order['order_status'])) ?>
                    </span>
                </div>
                <form
                    method="post"
                    action="<?= e(admin_url('order', ['id' => $order['id']])) ?>"
                >
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_order">
                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                    <label>
                Status Pesanan
                        <select name="order_status">
                            <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                                <option
                                    value="<?= e($status) ?>"
                                    <?= $order['order_status'] === $status ? 'selected' : '' ?>
                                >
                                <?= e(order_status_label($status)) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                Status Pembayaran
                            <select name="payment_status">
                                <?php foreach (['pending', 'unpaid', 'paid', 'failed', 'expired', 'refunded'] as $status): ?>
                                <option
                                    value="<?= e($status) ?>"
                                    <?= $order['payment_status'] === $status ? 'selected' : '' ?>
                                >
                                <?= e(payment_status_label($status)) ?>
                                </option>
                                <?php endforeach; ?>
                                </select>
                            </label>
                            <label>
                Kurir
                                <input
                                    type="text"
                                    name="courier"
                                    value="<?= e($order['courier'] ?? '') ?>"
                                    placeholder="Contoh: JNE Reguler"
                                >
                            </label>
                            <label>
                Nomor Resi
                                <input
                                    type="text"
                                    name="tracking_number"
                                    value="<?= e($order['tracking_number'] ?? '') ?>"
                                    placeholder="Masukkan nomor resi"
                                >
                            </label>
                            <button class="button button-primary button-full" type="submit">
                Simpan Perubahan
                            </button>
                        </form>
                        <div class="payment-admin-note">
                            <b>
                Metode pembayaran:
                            </b>
                            <p>
                                <?= e(payment_method_label($order['payment_method'], $payment['payment_type'] ?? null)) ?>
                            </p>
                            <?php if ($payment): ?>
                                <small>
                ID transaksi:
                                <?= e($payment['transaction_id'] ?: '-') ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            <a
                                class="button button-outline button-full"
                                href="<?= e(url('order', ['code' => $order['order_code']])) ?>"
                                target="_blank"
                            >
            Lihat Struk User ↗
                            </a>
                        </section>
                    </div>
                    <?php endif; ?>
