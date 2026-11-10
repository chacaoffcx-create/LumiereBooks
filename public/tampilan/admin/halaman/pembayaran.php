<section class="admin-panel">
    <div class="panel-heading">
        <div>
            <h2>
                Daftar Pembayaran
            </h2>
            <p>
                Riwayat transaksi pelanggan Lumière Books.
            </p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>
                        ID Transaksi
                    </th>
                    <th>
                        Pesanan
                    </th>
                    <th>
                        Pelanggan
                    </th>
                    <th>
                        Metode
                    </th>
                    <th>
                        Total
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Tanggal
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($payments as $payment): ?>
                    <tr>
                        <td>
                            <?= e($payment['transaction_id']??'-') ?>
                        </td>
                        <td>
                            <?= e($payment['order_code']) ?>
                        </td>
                        <td>
                            <?= e($payment['customer_name']) ?>
                            <small>
                                <?= e($payment['email']) ?>
                            </small>
                        </td>
                        <td>
                            <?= e($payment['payment_type']) ?>
                        </td>
                        <td>
                            <?= money($payment['amount']) ?>
                        </td>
                        <td>
                            <span
                                class="status-badge status-<?= e(status_class($payment['status'])) ?>"
                            >
                                <?= e(payment_status_label($payment['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?= date_id($payment['created_at'],true) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(!$payments): ?>
                        <tr>
                            <td colspan="7">
                        Belum ada transaksi.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
