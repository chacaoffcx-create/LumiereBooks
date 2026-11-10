<section class="container page-section about-page">
    <section class="about-page-hero" aria-label="Tentang Lumière Books">
        <div class="about-page-hero-copy">
            <span class="eyebrow">
                TENTANG KAMI
            </span>
            <h1>
                Tentang Kami
            </h1>
            <p>
                Mengenal lebih dekat Lumière Books,
                <br>
                tempat di mana setiap buku membawa cahaya baru.
            </p>
        </div>
        <div class="about-page-hero-dots" aria-hidden="true">
            •••
        </div>
    </section>
    <nav class="about-breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(url($user ? 'home' : 'welcome')) ?>">
            Beranda
        </a>
        <span>
            ›
        </span>
        <b>
            Tentang Kami
        </b>
    </nav>
    <section class="about-intro">
        <div class="about-photo">
            <img
                src="<?= e(asset('images/login-reading-corner.jpg')) ?>"
                alt="Sudut membaca Lumière Books dengan buku dan cangkir berlogo"
            >
        </div>
        <div class="about-story">
            <h2>
                Lumière Books
            </h2>
            <div class="about-title-ornament" aria-hidden="true">
                <span>
                </span>
                ✦
                <span>
                </span>
            </div>
            <p>
                Lumière Books adalah toko buku online yang menghadirkan berbagai koleksi buku pilihan, mulai
                dari fiksi, non-fiksi, pendidikan, hingga buku self-help. Kami percaya bahwa setiap buku
                memiliki kekuatan untuk membuka wawasan, menumbuhkan imajinasi, dan memberi inspirasi dalam
                setiap langkah kehidupan.
            </p>
            <div class="about-story-decoration" aria-hidden="true">
                <svg class="about-book-stack" viewBox="0 0 170 150" xmlns="http://www.w3.org/2000/svg">
                    <g
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M20 105c18-7 36-7 54 0v18c-18-7-36-7-54 0z"/>
                        <path d="M74 105c18-7 36-7 54 0v18c-18-7-36-7-54 0z"/>
                        <path d="M20 105l13-13c16-5 29-3 41 3v18c-12-6-25-8-41-3z"/>
                        <path d="M74 95c13-6 27-8 41-3l13 13v18c-14-5-28-3-41 3"/>
                        <path d="M13 86c20-9 42-9 63 0v17c-21-9-43-9-63 0z"/>
                        <path d="M76 86c21-9 43-9 63 0v17c-20-9-42-9-63 0z"/>
                        <path d="M25 69c18-8 35-8 51 0v17c-16-8-33-8-51 0z"/>
                        <path d="M76 69c16-8 33-8 51 0v17c-16-8-33-8-51 0z"/>
                        <path d="M137 22v18m-9-9h18M151 52v12m-6-6h12"/>
                        <path d="M19 37l3 7 7 3-7 3-3 7-3-7-7-3 7-3z"/>
                    </g>
                </svg>
            </div>
        </div>
    </section>
    <section class="about-values">
        <article class="about-value">
            <div class="about-value-heading">
                <span class="about-value-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48">
                        <path d="M5 10c7-3 13-2 19 2v27c-6-4-12-5-19-2V10Z"/>
                        <path d="M43 10c-7-3-13-2-19 2v27c6-4 12-5 19-2V10Z"/>
                        <path d="M24 12v27"/>
                    </svg>
                </span>
                <h3>
                    Visi
                </h3>
            </div>
            <p>
                Menjadi toko buku online terpercaya yang menghadirkan akses bacaan berkualitas untuk semua
                kalangan.
            </p>
        </article>
        <article class="about-value">
            <div class="about-value-heading">
                <span class="about-value-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48">
                        <circle cx="23" cy="25" r="17"/>
                        <circle cx="23" cy="25" r="10"/>
                        <circle cx="23" cy="25" r="3"/>
                        <path d="m25 23 15-15m-8 0h8v8"/>
                    </svg>
                </span>
                <h3>
                    Misi
                </h3>
            </div>
            <ul>
                <li>
                    Menyediakan koleksi buku yang lengkap, relevan, dan berkualitas.
                </li>
                <li>
                    Memberikan pengalaman belanja buku yang mudah, aman, dan menyenangkan.
                </li>
                <li>
                    Mendorong budaya literasi di masyarakat.
                </li>
            </ul>
        </article>
        <article class="about-value about-value-tags">
            <div class="about-value-heading">
                <span class="about-value-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48">
                        <path d="M24 41S5 29 5 16C5 6 18 3 24 14 30 3 43 6 43 16c0 13-19 25-19 25Z"/>
                    </svg>
                </span>
                <h3>
                    Nilai Kami
                </h3>
            </div>
            <div class="about-tags">
                <span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M19 4C10 4 5 8 5 15c0 3 2 5 5 5 7 0 9-7 9-16Z"/>
                        <path d="M4 21c3-5 7-8 12-11"/>
                    </svg>
                    Kualitas
                </span>
                <span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3 20 6v6c0 5-3 8-8 10-5-2-8-5-8-10V6l8-3Z"/>
                        <path d="m8 12 3 3 5-6"/>
                    </svg>
                    Kepercayaan
                </span>
                <span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M16 20v-1.5a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20"/>
                        <circle cx="9.5" cy="7" r="3.5"/>
                        <path d="M21 20v-1.5a4 4 0 0 0-3-3.9M16 3.3a3.5 3.5 0 0 1 0 6.8"/>
                    </svg>
                    Pelayanan
                </span>
                <span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"
                         />
                    </svg>
                    Inspirasi
                </span>
            </div>
        </article>
    </section>
    <section class="about-quote-banner">
        <div>
            <p>
                “Setiap buku adalah
                <br>
                pintu menuju dunia baru.”
            </p>
            <span>
            </span>
        </div>
    </section>
</section>
