<section class="container page-section narrow-section">
    <?php
    $isBankTransfer = $order['payment_method'] === 'bank_transfer';

    $isQris = $order['payment_method'] === 'qris';

    $virtualAccount = '9888' . str_pad((string) $order['id'], 12, '0', STR_PAD_LEFT);

    ?>
    <div class="payment-status-card">
        <img
            class="status-illustration"
            src="<?= e(asset('logo-lumiere.png')) ?>"
            alt="Logo Lumière Books"
        >
        <span class="eyebrow">
            PEMBAYARAN Lumière Books
        </span>
        <h1>
            <?= $order['payment_status'] === 'pending' ? 'Selesaikan Pembayaran' : 'Status Pembayaran' ?>
        </h1>
        <p>
            Nomor pesanan
            <b>
                <?= e($order['order_code']) ?>
            </b>
        </p>
        <div class="payment-amount">
            <?= money($order['total']) ?>
        </div>
        <div class="status-row">
            <span>
                Status pembayaran
            </span>
            <span class="status-badge status-<?= e(status_class($order['payment_status'])) ?>">
                <?= e(payment_status_label($order['payment_status'])) ?>
            </span>
        </div>
        <?php if ($order['payment_status'] === 'paid'): ?>
            <div class="success-panel">
                <b>
                ✓ Pembayaran berhasil.
                </b>
                <p>
                Pesanan sudah masuk ke antrean admin untuk diproses dan dikirim.
                </p>
            </div>
            <a
                class="button button-primary button-full"
                href="<?= e(url('order', ['code' => $order['order_code']])) ?>"
            >
            Lihat Pesanan & Struk
            </a>
            <?php elseif ($order['payment_status'] === 'failed' || $order['payment_status'] === 'expired'): ?>
            <div class="error-panel">
                <b>
                ✕ Pembayaran tidak berhasil.
                </b>
                <p>
                Pesanan dibatalkan dan stok buku telah dikembalikan. Silakan buat pesanan baru jika ingin
                mencoba lagi.
                </p>
            </div>
            <a class="button button-primary button-full" href="<?= e(url('my-orders')) ?>">
            Kembali ke Pesanan Saya
            </a>
            <?php elseif ($payment && $payment['provider'] === 'demo' && ($isBankTransfer || $isQris)): ?>
            <div class="payment-method-note">
                <b>
                Selesaikan pembayaran Anda.
                </b>
            Ikuti informasi pembayaran di bawah ini untuk melanjutkan pesanan.
            </div>
            <?php if ($isBankTransfer): ?>
                <div class="demo-payment-detail">
                    <small>
                Metode pembayaran
                    </small>
                    <b>
                        <?= e($payment['payment_type']) ?>
                    </b>
                    <small>
                Nomor Rekening / Virtual Account
                    </small>
                    <strong class="demo-va-number">
                        <?= e($virtualAccount) ?>
                    </strong>
                </div>
                <?php else: ?>
                <div class="demo-qris-card">
                    <div class="demo-qr-art" aria-label="Ilustrasi QRIS">
                        <span>
                    DEMO
                        </span>
                    </div>
                    <b>
                QRIS Lumière Books
                    </b>
                    <small>
                Pilih DANA, OVO, atau GoPay untuk melanjutkan pembayaran.
                    </small>
                </div>
                <?php endif; ?>
                <div class="demo-result-actions">
                    <form
                        method="post"
                        action="<?= e(url('payment', ['code' => $order['order_code']])) ?>"
                    >
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="demo_payment_result">
                        <input type="hidden" name="order_code" value="<?= e($order['order_code']) ?>">
                        <input type="hidden" name="result" value="paid">
                        <input
                            type="hidden"
                            name="_back"
                            value="<?= e(url('payment', ['code' => $order['order_code']])) ?>"
                        >
                        <button class="button button-primary button-full" type="submit">
                    Saya Sudah Membayar
                        </button>
                    </form>
                    <form
                        method="post"
                        action="<?= e(url('payment', ['code' => $order['order_code']])) ?>"
                    >
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="demo_payment_result">
                        <input type="hidden" name="order_code" value="<?= e($order['order_code']) ?>">
                        <input type="hidden" name="result" value="failed">
                        <input
                            type="hidden"
                            name="_back"
                            value="<?= e(url('payment', ['code' => $order['order_code']])) ?>"
                        >
                        <button class="button button-outline button-full" type="submit">
                    Pembayaran Tidak Berhasil
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="error-panel">
                    <b>
                Data pembayaran tidak ditemukan.
                    </b>
                    <p>
                Silakan periksa detail pesanan atau hubungi admin.
                    </p>
                </div>
                <a
                    class="button button-outline button-full"
                    href="<?= e(url('order', ['code' => $order['order_code']])) ?>"
                >
            Lihat Detail Pesanan
                </a>
                <?php endif; ?>
            </div>
        </section>
