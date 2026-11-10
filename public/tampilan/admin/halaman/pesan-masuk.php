<section class="admin-panel">
    <div class="panel-heading">
        <h2>
            Pesan dari Pelanggan
        </h2>
        <span>
            <?= count($messages) ?>
            pesan
        </span>
    </div>
    <div class="message-list">
        <?php foreach ($messages as $message): ?>
            <article class="message-card">
                <div class="message-top">
                    <div>
                        <b>
                            <?= e($message['subject']) ?>
                        </b>
                        <small>
                            <?= e($message['name']) ?>
                        ·
                            <?= e($message['email']) ?>
                        ·
                            <?= date_id($message['created_at'], true) ?>
                        </small>
                    </div>
                    <span
                        class="status-badge status-<?= e(
                            status_class($message['status'] === 'new' ? 'pending' : 'paid')
                        ) ?>"
                    >
                        <?= e(ucfirst($message['status'])) ?>
                    </span>
                </div>
                <p>
                    <?= nl2br(e($message['message'])) ?>
                </p>
                <div class="message-actions">
                    <a
                        class="button button-outline button-small"
                        href="mailto:<?= e($message['email']) ?>?subject=Re:%20<?= rawurlencode($message['subject']) ?>"
                    >
                    Balas via Email
                    </a>
                    <form method="post" action="<?= e(admin_url('messages')) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_message">
                        <input
                            type="hidden"
                            name="message_id"
                            value="<?= (int) $message['id'] ?>"
                        >
                        <select name="status">
                            <option
                                value="new"
                                <?= $message['status'] === 'new' ? 'selected' : '' ?>
                            >
                            Baru
                            </option>
                            <option
                                value="read"
                                <?= $message['status'] === 'read' ? 'selected' : '' ?>
                            >
                            Dibaca
                            </option>
                            <option
                                value="replied"
                                <?= $message['status'] === 'replied' ? 'selected' : '' ?>
                            >
                            Sudah dibalas
                            </option>
                        </select>
                        <button class="button button-primary button-small" type="submit">
                        Simpan
                        </button>
                    </form>
                </div>
            </article>
            <?php endforeach; ?>
            <?php if (!$messages): ?>
                <p class="muted">
            Belum ada pesan masuk.
                </p>
                <?php endif; ?>
            </div>
        </section>
