<section class="container account-layout help-account-layout">
    <div class="help-account-sidebar">
        <?php $accountActive = 'help'; include dirname(__DIR__) . '/bagian/menu-akun.php'; ?>
    </div>
    <div class="account-main help-page">
        <div class="help-page-inner">
            <div class="help-hero">
                <span class="eyebrow">
                    Lumière Books
                </span>
                <h1>
                    Pusat Bantuan
                </h1>
                <p>
                    Temukan panduan, jawaban pertanyaan umum, atau hubungi tim kami.
                </p>
                <form action="<?= e(url('books')) ?>" method="get">
                    <input type="search" name="q" placeholder="Cari bantuan atau buku...">
                    <button>
                        →
                    </button>
                </form>
            </div>
            <div class="help-topics">
                <a href="#faq">
                    ▣
                    <b>
                        Pesanan & Pembelian
                    </b>
                </a>
                <a href="#faq">
                    ▤
                    <b>
                        Pembayaran
                    </b>
                </a>
                <a href="#faq">
                    ♧
                    <b>
                        Pengiriman
                    </b>
                </a>
                <a href="<?= e(url('contact')) ?>">
                    ↻
                    <b>
                        Retur & Pengembalian
                    </b>
                </a>
                <a href="#faq">
                    ％
                    <b>
                        Promo & Voucher
                    </b>
                </a>
                <a href="<?= e(url('profile')) ?>">
                    ♙
                    <b>
                        Akun & Profil
                    </b>
                </a>
            </div>
            <div class="help-content">
                <section id="faq">
                    <h2>
                        Pertanyaan yang Sering Diajukan (FAQ)
                    </h2>
                    <?php $faqs=[['Bagaimana cara melakukan pemesanan?',
        'Pilih buku yang diinginkan, tambahkan ke keranjang, ' .
        'lanjutkan ke checkout, isi alamat, pilih kurir dan metode pembayaran, lalu buat pesanan.'],
    ['Metode pembayaran apa yang tersedia?',
        'Untuk praktik ini tersedia simulasi Transfer Bank BCA, ' .
        'Transfer Bank BRI, dan Cash on Delivery (COD). Tidak ada dana sungguhan yang diproses.'],
    ['Bagaimana cara melacak pesanan?',
        'Buka menu Pesanan Saya. Jika pesanan sudah dikirim, nomor resi dan pembaruan status akan ditampilkan.'],
    ['Bagaimana jika buku yang diterima rusak?',
        'Hubungi admin melalui halaman Kontak dengan menyertakan nomor pesanan dan penjelasan kondisi buku.'],
    ['Bagaimana cara menggunakan voucher?',
        'Masukkan kode voucher pada halaman checkout jika promo tersedia.']];
 foreach($faqs as $f): ?>
                        <details>
                            <summary>
                                <?= e($f[0]) ?>
                            </summary>
                            <p>
                                <?= e($f[1]) ?>
                            </p>
                        </details>
                        <?php endforeach; ?>
                    </section>
                    <aside class="help-contact">
                        <h2>
                        Belum Menemukan Jawaban?
                        </h2>
                        <p>
                        Hubungi tim Lumière Books, kami siap membantu.
                        </p>
                        <a
                            class="button button-primary"
                            href="https://wa.me/6281234567890"
                            target="_blank"
                            rel="noopener"
                        >
                        Chat via WhatsApp
                        </a>
                        <a class="button button-outline" href="mailto:support@lumierebooks.id">
                        Kirim Email
                        </a>
                        <a class="button button-outline" href="<?= e(url('contact')) ?>">
                        Formulir Kontak
                        </a>
                    </aside>
                </div>
            </section>
        </div>
    </div>
    </section>
