<section class="container page-section">
    <div class="page-title-row">
        <div>
            <span class="eyebrow">
                JELAJAHI BERDASARKAN GENRE
            </span>
            <h1>
                Semua Kategori
            </h1>
            <p>
                Temukan bacaan yang sesuai dengan minat dan suasana hatimu.
            </p>
        </div>
    </div>
    <div class="category-grid all-category-grid">
        <?php foreach ($categories as $category): ?>
            <?php
                $count = db()->prepare(
                    'SELECT COUNT(*) FROM books WHERE category_id = ? AND status = "active"'
                );

                $count->execute([$category['id']]);

            ?>
            <a
                class="category-card redesigned-category-card"
                href="<?= e(url('books', ['category' => $category['slug']])) ?>"
            >
                <img
                    class="category-card-photo category-card-photo-background"
                    src="<?= e(lumiere_category_photo_url($category)) ?>"
                    alt=""
                    aria-hidden="true"
                    loading="lazy"
                    onerror="this.style.display='none'"
                >
                <span class="category-symbol">
                    <?= ui_icon('book-open') ?>
                </span>
                <b>
                    <?= e($category['name']) ?>
                </b>
                <small>
                    <?= (int) $count->fetchColumn() ?>
                Buku
                </small>
                <span class="circle-arrow">
                →
                </span>
            </a>
            <?php endforeach; ?>
        </div>
        <section class="category-note">
            <div>
                <h2>
                Setiap genre punya cerita.
                </h2>
                <p>
                Jelajahi koleksi pilihan Lumière Books dan temukan buku yang akan menemani perjalananmu.
                </p>
            </div>
            <span class="category-note-art">
            ✦ 📚 ✦
            </span>
        </section>
    </section>
