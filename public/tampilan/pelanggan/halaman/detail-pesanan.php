<?php
$status = (string) ($order['order_status'] ?? 'pending');

$statusSteps = [
    'pending' => ['label' => 'Pesanan Dibuat',
         'icon' => 'clipboard-list'],
    
    'processing' => ['label' => 'Dikemas',
         'icon' => 'package-open'],
    
    'shipped' => ['label' => 'Dikirim',
         'icon' => 'truck'],
    
    'delivered' => ['label' => 'Selesai',
         'icon' => 'circle-check'],
    
];

$stepKeys = array_keys($statusSteps);

$currentStep = array_search($status, $stepKeys, true);

if ($currentStep === false) $currentStep = -1;

$shippingMethod = trim((string) ($order['courier'] ?? '')) ?: 'Kurir belum ditentukan';

$paymentLabel = payment_method_label((string) $order['payment_method'], $payment['payment_type'] ?? null);

$quantityTotal = array_sum(array_map(static fn($item) => (int) $item['quantity'], $items));

$tracking = trim((string) ($order['tracking_number'] ?? ''));

?>
<section class="container order-detail-page">
    <nav class="order-breadcrumb" aria-label="Jejak halaman">
        <a href="<?= e(url('home')) ?>">
            <?= ui_icon('home') ?>
            Beranda
        </a>
        <span>
            ›
        </span>
        <a href="<?= e(url('my-orders')) ?>">
            Pesanan Saya
        </a>
        <span>
            ›
        </span>
        <b>
            Detail Pesanan
        </b>
    </nav>
    <div class="order-detail-heading">
        <a class="order-back-link" href="<?= e(url('my-orders')) ?>" aria-label="Kembali ke pesanan">
            <?= ui_icon('arrow-right') ?>
        </a>
        <div>
            <span class="eyebrow">
                PANTAU PESANANMU
            </span>
            <h1>
                Detail Pesanan
            </h1>
            <p>
                Informasi lengkap pembelian dan perjalanan pesananmu.
            </p>
        </div>
    </div>
    <div class="order-detail-grid">
        <main class="order-detail-main">
            <section class="order-tracking-card">
                <div class="order-tracking-topline">
                    <span>
                        Perjalanan Pesanan
                    </span>
                    <span class="order-status-badge <?= e(status_class($status)) ?>">
                        <?= e(order_status_label($status)) ?>
                    </span>
                </div>
                <?php if ($status === 'cancelled'): ?>
                    <div class="order-cancelled-state">
                        <?= ui_icon('circle-x') ?>
                        <div>
                            <b>
                            Pesanan Dibatalkan
                            </b>
                            <small>
                            Pesanan ini tidak lagi diproses.
                            </small>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="order-timeline">
                        <?php foreach ($statusSteps as $key => $step): ?>
                            <?php $index = array_search($key, $stepKeys, true);
                                $isDone = $index <= $currentStep; $isCurrent = $index === $currentStep;
                                ?>
                            <div
                                class="order-timeline-step <?= $isDone ? 'is-done' : '' ?> <?= $isCurrent ? 'is-current' : '' ?>"
                            >
                                <span class="order-timeline-icon">
                                <?= ui_icon($step['icon']) ?>
                                </span>
                                <b>
                                <?= e($step['label']) ?>
                                </b>
                                <small>
                                <?php if ($index === 0): ?>
                                <?= e(date_id($order['created_at'], true)) ?>
                                <?php elseif ($isCurrent): ?>
                            Status terkini
                                <?php elseif ($isDone): ?>
                            Selesai
                                <?php else: ?>
                            Menunggu
                                <?php endif; ?>
                                </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </section>
                        <section class="order-detail-card order-products-card">
                            <div class="order-meta-grid">
                                <div class="order-meta-item">
                                <span class="order-meta-icon">
                                <?= ui_icon('clipboard-list') ?>
                                </span>
                                <div>
                                <small>
                                Nomor Pesanan
                                </small>
                                <b>
                                <?= e($order['order_code']) ?>
                                </b>
                                </div>
                                </div>
                                <div class="order-meta-item">
                                <span class="order-meta-icon">
                                <?= ui_icon('calendar') ?>
                                </span>
                                <div>
                                <small>
                                Tanggal Pemesanan
                                </small>
                                <b>
                                <?= e(date_id($order['created_at'], true)) ?>
                                </b>
                                </div>
                                </div>
                                <div class="order-meta-status">
                                <span class="order-status-badge <?= e(status_class($status)) ?>">
                                <?= e(order_status_label($status)) ?>
                                </span>
                                </div>
                            </div>
                            <div class="order-card-divider">
                            </div>
                            <h2 class="order-card-title">
                                <?= ui_icon('cart') ?>
                    Daftar Produk
                                <small>
                                <?= (int) $quantityTotal ?>
                        buku
                                </small>
                            </h2>
                            <div class="order-product-list">
                                <?php foreach ($items as $item): ?>
                                <article class="order-product-row">
                                <img
                                    class="order-product-cover"
                                    src="<?= e(cover_url($item['cover'] ?? null)) ?>"
                                    alt="Sampul <?= e($item['title']) ?>"
                                >
                                <div class="order-product-info">
                                <b>
                                <?= e($item['title']) ?>
                                </b>
                                <span>
                                <?= e($item['author']) ?>
                                </span>
                                </div>
                                <div class="order-product-unit">
                                <?= money($item['price']) ?>
                                </div>
                                <div class="order-product-qty">
                            ×
                                <?= (int) $item['quantity'] ?>
                                </div>
                                <b class="order-product-subtotal">
                                <?= money($item['subtotal']) ?>
                                </b>
                                </article>
                                <?php endforeach; ?>
                                </div>
                                <div class="order-card-divider">
                                </div>
                                <div class="order-cost-lines">
                                <div>
                                <span>
                            Subtotal (
                                <?= (int) $quantityTotal ?>
                            Buku)
                                </span>
                                <b>
                                <?= money($order['subtotal']) ?>
                                </b>
                                </div>
                                <div>
                                <span>
                            Ongkos Kirim (
                                <?= e($shippingMethod) ?>
                            )
                                </span>
                                <b>
                                <?= money($order['shipping_cost']) ?>
                                </b>
                                </div>
                                <div class="order-grand-total">
                                <span>
                            Total Pembayaran
                                </span>
                                <b>
                                <?= money($order['total']) ?>
                                </b>
                                </div>
                                </div>
                            </section>
                            <section class="order-detail-card order-payment-proof">
                                <h2 class="order-card-title">
                                <?= ui_icon('credit-card') ?>
                    Informasi Pembayaran
                                </h2>
                                <div class="order-payment-details">
                                <div>
                                <small>
                            Metode Pembayaran
                                </small>
                                <b>
                                <?= e($paymentLabel) ?>
                                </b>
                                </div>
                                <div>
                                <small>
                            Status Pembayaran
                                </small>
                                <b>
                                <span
                                    class="order-status-badge <?= e(status_class($order['payment_status'])) ?>"
                                >
                                <?= e(payment_status_label($order['payment_status'])) ?>
                                </span>
                                </b>
                                </div>
                                <?php if ($payment): ?>
                                <div>
                                <small>
                            ID Transaksi
                                </small>
                                <b>
                                <?= e($payment['transaction_id'] ?: $payment['order_id']) ?>
                                </b>
                                </div>
                                <?php endif; ?>
                                </div>
                                </section>
                            </main>
                            <aside class="order-detail-aside">
                                <section class="order-detail-card order-aside-card">
                                <h2 class="order-card-title">
                                <?= ui_icon('map-pin') ?>
                    Informasi Pengiriman
                                </h2>
                                <div class="order-recipient">
                                <b>
                                <?= e($order['recipient_name']) ?>
                                </b>
                                <span>
                                <?= e($order['phone']) ?>
                                </span>
                                <p>
                                <?= nl2br(e($order['shipping_address'])) ?>
                                <br>
                                <?= e($order['city']) ?>
                        ,
                                <?= e($order['province']) ?>
                                <?= e($order['postal_code']) ?>
                                </p>
                                </div>
                                </section>
                                <section class="order-detail-card order-aside-card">
                                <h2 class="order-card-title">
                                <?= ui_icon('truck') ?>
                    Metode Pengiriman
                                </h2>
                                <div class="order-courier-row">
                                <span
                                    class="order-courier-mark"
                                    aria-label="Logo <?= e($shippingMethod) ?>"
                                >
                                <?php if (stripos($shippingMethod, 'jne') !== false): ?>
                                <svg viewBox="0 0 130 42" role="img" aria-hidden="true">
                                <path d="M8 8h37L32 34H5z" fill="#d52c32"/>
                                <path d="M20 8h14L18 34H5z" fill="#174b91"/>
                                <text
                                    x="43"
                                    y="27"
                                    font-family="Arial,sans-serif"
                                    font-size="27"
                                    font-weight="900"
                                    font-style="italic"
                                    fill="#174b91"
                                >
                                JNE
                                </text>
                                <text
                                    x="44"
                                    y="37"
                                    font-family="Arial,sans-serif"
                                    font-size="7"
                                    font-weight="700"
                                    fill="#d52c32"
                                >
                                <?= stripos($shippingMethod, 'reguler') !== false ? 'REGULER' : 'EXPRESS' ?>
                                </text>
                                </svg>
                                <?php elseif (stripos($shippingMethod, 'sicepat') !== false): ?>
                                <svg viewBox="0 0 142 42" role="img" aria-hidden="true">
                                <path d="M5 9h31l-8 24H1z" fill="#d82b32"/>
                                <path d="M14 14l12 4-8 11z" fill="#fff"/>
                                <text
                                    x="39"
                                    y="27"
                                    font-family="Arial,sans-serif"
                                    font-size="21"
                                    font-weight="900"
                                    font-style="italic"
                                    fill="#c9252c"
                                >
                                SiCepat
                                </text>
                                </svg>
                                <?php elseif (stripos($shippingMethod, 'pos') !== false): ?>
                                <svg viewBox="0 0 142 42" role="img" aria-hidden="true">
                                <rect
                                    x="5"
                                    y="9"
                                    width="26"
                                    height="24"
                                    rx="3"
                                    fill="#f18a22"
                                 />
                                <path d="M11 15h14M11 21h14M11 27h9" stroke="#fff" stroke-width="2.5"/>
                                <text
                                    x="36"
                                    y="27"
                                    font-family="Arial,sans-serif"
                                    font-size="21"
                                    font-weight="800"
                                    fill="#d8791c"
                                >
                                POS
                                </text>
                                <text
                                    x="38"
                                    y="36"
                                    font-family="Arial,sans-serif"
                                    font-size="7"
                                    font-weight="700"
                                    fill="#9b4b16"
                                >
                                INDONESIA
                                </text>
                                </svg>
                                <?php elseif (stripos($shippingMethod, 'j&t') !== false || stripos($shippingMethod, 'jnt') !== false): ?>
                                <svg viewBox="0 0 142 42" role="img" aria-hidden="true">
                                <path d="M5 9h28l-8 24H1z" fill="#d92d32"/>
                                <path d="M13 13l12 6-10 11z" fill="#fff"/>
                                <text
                                    x="37"
                                    y="27"
                                    font-family="Arial,sans-serif"
                                    font-size="24"
                                    font-weight="900"
                                    fill="#d52b32"
                                >
                                J&amp;T
                                </text>
                                <text
                                    x="83"
                                    y="26"
                                    font-family="Arial,sans-serif"
                                    font-size="9"
                                    font-weight="700"
                                    fill="#d52b32"
                                >
                                EXPRESS
                                </text>
                                </svg>
                                <?php else: ?>
                                <b>
                                <?= e(strtoupper($shippingMethod)) ?>
                                </b>
                                <?php endif; ?>
                                </span>
                                <div>
                                <b>
                                <?= e($shippingMethod) ?>
                                </b>
                                <small>
                                <?= $tracking !== '' ? 'Nomor resi: ' . e($tracking) : 'Nomor resi belum tersedia' ?>
                                </small>
                                </div>
                                </div>
                                <?php if ($tracking !== ''): ?>
                                <p class="order-tracking-note">
                    Simpan nomor resi untuk memantau perjalanan paketmu melalui layanan resmi kurir.
                                </p>
                                <?php endif; ?>
                                </section>
                                <section class="order-detail-card order-aside-card">
                                <h2 class="order-card-title">
                                <?= ui_icon('wallet') ?>
                    Metode Pembayaran
                                </h2>
                                <div class="order-payment-method-row">
                                <span class="order-payment-method-icon">
                                <?=ui_icon($order['payment_method'] === 'cod' ?
                                    'money' : ($order['payment_method'] === 'qris' ?
                                    'wallet' : 'classical'))?>
                                </span>
                                <div>
                                <b>
                                <?= e($paymentLabel) ?>
                                </b>
                                <small>
                                <?= e(payment_status_label($order['payment_status'])) ?>
                                </small>
                                </div>
                                </div>
                                </section>
                                <a class="order-support-button" href="<?= e(url('contact')) ?>">
                                <?= ui_icon('headset') ?>
                Hubungi Layanan Pelanggan
                                </a>
                                <?php if (in_array($order['payment_method'], ['bank_transfer',
             'qris'], true) && in_array($order['payment_status'], ['pending',
             'unpaid'], true) && $status !== 'cancelled'): ?>
                                <a
                                    class="order-pay-button"
                                    href="<?= e(url('payment', ['code' => $order['order_code']])) ?>"
                                >
                                <?= ui_icon('lock') ?>
                Lanjutkan Pembayaran
                                </a>
                                <?php endif; ?>
                                </aside>
                                </div>
                                <div class="order-detail-bottom-actions">
                                <a class="button button-outline" href="<?= e(url('my-orders')) ?>">
            Kembali ke Pesanan
                                </a>
                                <button
                                    class="button button-primary"
                                    type="button"
                                    onclick="window.print()"
                                >
                                <?= ui_icon('file-text') ?>
            Cetak Detail Pesanan
                                </button>
                                </div>
                                </section>
