<section class="admin-stat-grid reports-stat-grid">
    <article class="admin-stat">
        <span class="stat-icon stat-icon-money">
            <?= ui_icon('money') ?>
        </span>
        <div>
            <small>
                Total Pendapatan
            </small>
            <b>
                <?= money($metrics['revenue']) ?>
            </b>
            <em>
                Transaksi berhasil
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('box') ?>
        </span>
        <div>
            <small>
                Total Pesanan
            </small>
            <b>
                <?= number_format($metrics['orders'], 0, ',', '.') ?>
            </b>
            <em>
                Semua status
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('users') ?>
        </span>
        <div>
            <small>
                Total Pelanggan
            </small>
            <b>
                <?= number_format($metrics['users'], 0, ',', '.') ?>
            </b>
            <em>
                Akun user
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('book-open') ?>
        </span>
        <div>
            <small>
                Rata-rata Nilai Pesanan
            </small>
            <b>
                <?= money($metrics['average_order']) ?>
            </b>
            <em>
                Per transaksi berhasil
            </em>
        </div>
    </article>
</section>
<section class="report-chart-grid">
    <article class="admin-panel report-monthly-panel">
        <div class="panel-heading">
            <div>
                <h2>
                    <?= ui_icon('chart') ?>
                    Grafik Pendapatan Bulanan
                </h2>
                <p>
                    Pergerakan pendapatan dari transaksi terbayar.
                </p>
            </div>
            <span class="panel-select">
                12 bulan terakhir
            </span>
        </div>
        <?php if ($monthly): ?>
            <div class="chart-wrap">
                <canvas id="reportRevenueChart" height="185">
                </canvas>
            </div>
            <?php else: ?>
            <div class="chart-empty">
                <?= ui_icon('chart') ?>
                <b>
                Belum ada data pendapatan
                </b>
                <span>
                Grafik akan terisi setelah pembayaran berhasil.
                </span>
            </div>
            <?php endif; ?>
        </article>
        <article class="admin-panel report-category-panel">
            <div class="panel-heading">
                <div>
                    <h2>
                    Pendapatan per Kategori
                    </h2>
                    <p>
                    Kontribusi setiap kategori buku.
                    </p>
                </div>
            </div>
            <?php if ($byCategory): ?>
                <div class="report-donut-content">
                    <div class="report-donut">
                        <canvas id="categoryChart">
                        </canvas>
                        <div>
                            <b>
                                <?= money($metrics['revenue']) ?>
                            </b>
                            <small>
                        Total
                            </small>
                        </div>
                    </div>
                    <div class="report-category-legend">
                        <?php foreach ($byCategory as $category): ?>
                            <div>
                                <span>
                                <?= e($category['name']) ?>
                                </span>
                                <b>
                                <?= money($category['revenue']) ?>
                                </b>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="chart-empty compact">
                        <?= ui_icon('chart') ?>
                        <b>
                Belum ada transaksi terbayar
                        </b>
                        <span>
                Ringkasan kategori akan muncul di sini.
                        </span>
                    </div>
                    <?php endif; ?>
                </article>
            </section>
            <section class="report-bottom-grid">
                <article class="admin-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>
                                <?= ui_icon('wallet') ?>
                    Pendapatan per Metode Pembayaran
                            </h2>
                            <p>
                    Perbandingan transaksi berhasil.
                            </p>
                        </div>
                    </div>
                    <?php if ($paymentMethods): ?>
                        <div class="chart-wrap payment-chart-wrap">
                            <canvas id="paymentMethodChart" height="150">
                            </canvas>
                        </div>
                        <?php else: ?>
                        <div class="chart-empty compact">
                            <?= ui_icon('wallet') ?>
                            <b>
                Belum ada data pembayaran
                            </b>
                        </div>
                        <?php endif; ?>
                    </article>
                    <article class="admin-panel report-transactions-panel">
                        <div class="panel-heading">
                            <div>
                                <h2>
                                <?= ui_icon('file') ?>
                    Transaksi Terbaru
                                </h2>
                                <p>
                    Riwayat pesanan terbaru dari pelanggan.
                                </p>
                            </div>
                            <a href="<?= e(admin_url('orders')) ?>">
                Lihat Semua
                                <?= ui_icon('arrow-right') ?>
                            </a>
                        </div>
                        <div class="table-wrap">
                            <table class="data-table compact-table">
                                <thead>
                                <tr>
                                <th>
                            No.
                                </th>
                                <th>
                            Tanggal
                                </th>
                                <th>
                            User
                                </th>
                                <th>
                            Total
                                </th>
                                <th>
                            Status
                                </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($recentTransactions as $index => $transaction): ?>
                                <tr>
                                <td>
                                <?= $index + 1 ?>
                                </td>
                                <td>
                                <?= date_id($transaction['created_at']) ?>
                                </td>
                                <td>
                                <?= e($transaction['customer_name']) ?>
                                </td>
                                <td>
                                <?= money($transaction['total']) ?>
                                </td>
                                <td>
                                <span
                                    class="status-badge status-<?= e(status_class($transaction['order_status'])) ?>"
                                >
                                <?= e(order_status_label($transaction['order_status'])) ?>
                                </span>
                                </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (!$recentTransactions): ?>
                                <tr>
                                <td colspan="5" class="table-empty">
                            Belum ada transaksi.
                                </td>
                                </tr>
                                <?php endif; ?>
                                </tbody>
                                </table>
                                </div>
                            </article>
                        </section>
                        <section class="report-summary-banner">
                            <div>
                                <span class="eyebrow">
            RINGKASAN LAPORAN
                                </span>
                                <h2>
            Perkembangan Lumière Books
                                </h2>
                                <p>
            Saat ini Lumière Books memiliki
                                <?= number_format($metrics['books'], 0, ',', '.') ?>
            buku dalam
                                <?= number_format($metrics['categories'], 0, ',', '.') ?>
            kategori. Total pendapatan yang sudah terkonfirmasi sebesar
                                <b>
                                <?= money($metrics['revenue']) ?>
                                </b>
            .
                                </p>
                            </div>
                            <img src="<?= e(asset('images/promo-sale.jpg')) ?>" alt="">
                        </section>
                        <?php if ($monthly || $byCategory || $paymentMethods): ?>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js">

</script>
                                <script>
window.ashReportData = {
    months:
    <?= json_encode(array_column($monthly, 'month_name')) ?>
    ,
    revenue:
    <?= json_encode(array_map(
                'floatval',
                array_column($monthly, 'revenue')
            )) ?>
    ,
    categories:
    <?= json_encode(array_map(
                static fn ($row) => $row['name'],
                $byCategory
            )) ?>
    ,
    categoryRevenue:
    <?= json_encode(array_map(
                'floatval',
                array_column($byCategory, 'revenue')
            )) ?>
    ,
    paymentMethods:
    <?= json_encode(array_map(
                static fn ($row) => payment_method_label($row['payment_method']),
        
                $paymentMethods
            )) ?>
    ,
    paymentRevenue:
    <?= json_encode(array_map(
                'floatval',
                array_column($paymentMethods, 'revenue')
            )) ?>
};
</script>
                                <?php endif; ?>
