<section class="container account-layout">
    <?php $accountActive = 'notifications'; include dirname(__DIR__) . '/bagian/menu-akun.php'; ?>
    <div class="account-main">
        <div class="section-heading">
            <div>
                <h1>
                    Notifikasi
                </h1>
                <p>
                    Informasi pesanan, pembayaran, pengiriman, dan promo.
                </p>
            </div>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="notifications_read_all">
                <input type="hidden" name="_back" value="<?= e(url('notifications')) ?>">
                <button class="button button-outline">
                    Tandai Semua Dibaca
                </button>
            </form>
            <form method="post" onsubmit="return confirm('Hapus semua notifikasi?')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="notifications_delete_all">
                <input type="hidden" name="_back" value="<?= e(url('notifications')) ?>">
                <button class="button button-outline">
                    Hapus Semua
                </button>
            </form>
        </div>
        <?php if($notifications): foreach($notifications as $n): ?>
            <article class="notification-card <?= !$n['is_read']?'unread':'' ?>">
                <span class="notification-icon">
                ♧
                </span>
                <div>
                    <h3>
                        <?= e($n['title']) ?>
                    </h3>
                    <p>
                        <?= e($n['message']) ?>
                    </p>
                    <small>
                        <?= date_id($n['created_at'],true) ?>
                    </small>
                </div>
                <?php if(!$n['is_read']): ?>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="notification_read">
                        <input type="hidden" name="_back" value="<?= e(url('notifications')) ?>">
                        <input type="hidden" name="notification_id" value="<?= (int)$n['id'] ?>">
                        <button>
                    Tandai Dibaca
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php if($n['target_url']): ?>
                        <a href="<?= e($n['target_url']) ?>">
                Lihat →
                        </a>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; else: ?>
                    <div class="empty-state">
                        <h2>
                Belum ada notifikasi
                        </h2>
                        <p>
                Informasi terbaru akan muncul di halaman ini.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
