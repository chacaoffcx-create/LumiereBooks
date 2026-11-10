<?php
$statusLabels = [
    'pending' => 'Menunggu Pembayaran',
    
    'processing' => 'Diproses',
    
    'shipped' => 'Dikirim',
    
    'delivered' => 'Selesai',
    
    'cancelled' => 'Dibatalkan',
    
];

$statusColors = [
    'pending' => '#7b421e',
    
    'processing' => '#b57b49',
    
    'shipped' => '#d6a77b',
    
    'delivered' => '#f0d7bd',
    
    'cancelled' => '#b96c5c',
    
];

?>
<section class="admin-stat-grid dashboard-stat-grid">
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('book-open') ?>
        </span>
        <div>
            <small>
                Total Buku
            </small>
            <b>
                <?= number_format($metrics['books'], 0, ',', '.') ?>
            </b>
            <em>
                Koleksi katalog
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('cart') ?>
        </span>
        <div>
            <small>
                Pesanan
            </small>
            <b>
                <?= number_format($metrics['orders'], 0, ',', '.') ?>
            </b>
            <em>
                <?= number_format($metrics['pending'], 0, ',', '.') ?>
                perlu diproses
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
            <?= ui_icon('users') ?>
        </span>
        <div>
            <small>
                Pelanggan
            </small>
            <b>
                <?= number_format($metrics['users'], 0, ',', '.') ?>
            </b>
            <em>
                Akun pelanggan
            </em>
        </div>
    </article>
    <article class="admin-stat">
        <span class="stat-icon">
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
                Pembayaran terkonfirmasi
            </em>
        </div>
    </article>
</section>
<section class="admin-dashboard-grid dashboard-analytics-grid">
    <article class="admin-panel sales-chart-panel">
        <div class="panel-heading">
            <div>
                <h2>
                    <?= ui_icon('chart') ?>
                    Penjualan 7 Hari Terakhir
                </h2>
                <p>
                    Jumlah pesanan yang masuk setiap hari.
                </p>
            </div>
            <span class="panel-period">
                7 hari terakhir
            </span>
        </div>
        <div class="admin-chart-wrap">
            <canvas id="sales7Chart" height="190">
            </canvas>
        </div>
    </article>
    <article class="admin-panel status-chart-panel">
        <div class="panel-heading">
            <div>
                <h2>
                    <?= ui_icon('chart') ?>
                    Status Pesanan
                </h2>
                <p>
                    Ringkasan status seluruh pesanan.
                </p>
            </div>
        </div>
        <?php if ($orderStatusSummary): ?>
            <div class="status-chart-content">
                <div class="status-donut">
                    <canvas id="orderStatusChart">
                    </canvas>
                    <div>
                        <b>
                            <?= number_format($metrics['orders'], 0, ',', '.') ?>
                        </b>
                        <small>
                        Pesanan
                        </small>
                    </div>
                </div>
                <div class="status-legend">
                    <?php foreach ($orderStatusSummary as $row): ?>
                        <div>
                            <span>
                                <i
                                    class="status-dot"
                                    style="background:<?= e($statusColors[$row['order_status']] ?? '#b98b68') ?>"
                                >
                                </i>
                                <?= e($statusLabels[$row['order_status']] ?? ucfirst($row['order_status'])) ?>
                            </span>
                            <b>
                                <?= number_format((int) $row['total'], 0, ',', '.') ?>
                            </b>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="chart-empty compact">
                    <?= ui_icon('cart') ?>
                    <b>
                Belum ada pesanan
                    </b>
                    <span>
                Ringkasan status akan muncul di sini.
                    </span>
                </div>
                <?php endif; ?>
            </article>
        </section>
        <section class="admin-dashboard-grid dashboard-bottom-grid">
            <article class="admin-panel best-sellers-panel">
                <div class="panel-heading">
                    <div>
                        <h2>
                            <?= ui_icon('star') ?>
                    Buku Terlaris
                        </h2>
                        <p>
                    Buku dengan jumlah penjualan tertinggi.
                        </p>
                    </div>
                    <a href="<?= e(admin_url('books')) ?>">
                Lihat Semua
                        <?= ui_icon('arrow-right') ?>
                    </a>
                </div>
                <?php if ($topBooks): ?>
                    <div class="best-seller-list">
                        <?php foreach ($topBooks as $rank => $book): ?>
                            <div class="best-seller-row">
                                <span class="best-seller-rank rank-<?= (int) $rank + 1 ?>">
                                <?= (int) $rank + 1 ?>
                                </span>
                                <?php if (!empty($book['cover'])): ?>
                                <img
                                    class="best-seller-cover"
                                    src="<?= e(cover_url($book['cover'])) ?>"
                                    alt="<?= e($book['title'] ?: 'Sampul buku') ?>"
                                >
                                <?php else: ?>
                                <span class="best-seller-cover best-seller-cover-empty">
                                <?= ui_icon('book-open') ?>
                                </span>
                                <?php endif; ?>
                                <span class="best-seller-copy">
                                <b>
                                <?= e($book['title'] ?: 'Buku tidak tersedia') ?>
                                </b>
                                <small>
                                <?= e($book['author'] ?: 'Penulis tidak tersedia') ?>
                                </small>
                                </span>
                                <strong>
                                <?= number_format((int) $book['units_sold'], 0, ',', '.') ?>
                                <small>
                        eksemplar
                                </small>
                                </strong>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="chart-empty compact">
                                <?= ui_icon('book-open') ?>
                                <b>
                Belum ada data penjualan
                                </b>
                                <span>
                Buku terlaris akan tampil setelah pembayaran terkonfirmasi.
                                </span>
                            </div>
                            <?php endif; ?>
                        </article>
                        <article class="admin-panel latest-orders-panel">
                            <div class="panel-heading">
                                <div>
                                <h2>
                                <?= ui_icon('file-text') ?>
                    Pesanan Terbaru
                                </h2>
                                <p>
                    Aktivitas pesanan yang baru masuk.
                                </p>
                                </div>
                                <a href="<?= e(admin_url('orders')) ?>">
                Lihat Semua
                                <?= ui_icon('arrow-right') ?>
                                </a>
                            </div>
                            <?php if ($latestOrders): ?>
                                <div class="admin-table-scroll">
                                <table class="admin-table dashboard-orders-table">
                                <thead>
                                <tr>
                                <th>
                            No. Pesanan
                                </th>
                                <th>
                            Pelanggan
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
                                <?php foreach ($latestOrders as $order): ?>
                                <tr>
                                <td>
                                <a
                                    class="dashboard-order-link"
                                    href="<?= e(admin_url('order', ['id' => $order['id']])) ?>"
                                >
                                <?= e($order['order_code']) ?>
                                </a>
                                </td>
                                <td>
                                <?= e($order['customer_name']) ?>
                                </td>
                                <td>
                                <b>
                                <?= money($order['total']) ?>
                                </b>
                                </td>
                                <td>
                                <span
                                    class="status-badge status-<?= e(status_class($order['order_status'])) ?>"
                                >
                                <?= e(order_status_label($order['order_status'])) ?>
                                </span>
                                </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                                </table>
                                </div>
                                <?php else: ?>
                                <div class="chart-empty compact">
                                <?= ui_icon('cart') ?>
                                <b>
                Belum ada pesanan
                                </b>
                                <span>
                Pesanan pelanggan akan tampil di sini.
                                </span>
                                </div>
                                <?php endif; ?>
                                </article>
                            </section>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js">

</script>
                                <script>
(() => {
    if (!window.Chart) return;
    const salesCanvas = document.getElementById('sales7Chart');
    if (salesCanvas) new Chart(salesCanvas, {
        type: 'line',
        data: {
            labels:
            <?= json_encode($sales7Labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
            ,
            datasets: [ {
                label: 'Pesanan',
                data:
                <?= json_encode($sales7Values) ?>
                ,
                borderColor: '#98643c',
                backgroundColor: 'rgba(190, 145, 103, .18)',
                fill: true,
                tension: .35,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#98643c',
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true, ticks: {
                        precision: 0, color: '#795d49'
                    }, grid: {
                        color: '#f0e6dc'
                    }
                },
                x: {
                    ticks: {
                        color: '#795d49'
                    }, grid: {
                        display: false
                    }
                }
            }
        }
    });
    const statusCanvas = document.getElementById('orderStatusChart');
    if (statusCanvas) new Chart(statusCanvas, {
        type: 'doughnut',
        data: {
            labels:
            <?= json_encode(array_map(static fn ($row) => $statusLabels[$row['order_status']] ?? ucfirst($row['order_status']),
         $orderStatusSummary), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
            ,
            datasets: [ {
                data:
                <?= json_encode(array_map('intval', array_column($orderStatusSummary, 'total'))) ?>
                ,
                backgroundColor:
                <?= json_encode(array_map(static fn ($row) => $statusColors[$row['order_status']] ?? '#b98b68', $orderStatusSummary)) ?>
                ,
                borderWidth: 2,
                borderColor: '#fffaf6',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
})();
</script>
