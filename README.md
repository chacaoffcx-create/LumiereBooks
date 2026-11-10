# Lumière Books — Bookstore Online (USK)

Website toko buku online berbasis PHP 8+, MySQL/MariaDB, HTML, CSS, dan JavaScript. Dibuat untuk praktik USK dengan simulasi pembayaran (bukan transaksi bank sungguhan).

## Fitur pelanggan
- Halaman depan penuh sebelum login dan beranda pelanggan setelah login.
- Registrasi, login, logout, pencarian, kategori, buku baru, buku terlaris, detail buku, Tentang Kami, dan Kontak.
- Keranjang, checkout, pilihan pengiriman, pembayaran simulasi BCA/BRI atau COD.
- Struk pembelian, simulasi pembayaran berhasil/gagal, Pesanan Saya, status pengantaran dan nomor resi.
- Profil, ubah kata sandi, Buku Favorit, Alamat Saya, Notifikasi, dan Pusat Bantuan.

## Fitur admin
- Dashboard ringkasan, CRUD buku dan kategori beserta foto, daftar pelanggan, pesanan, pembayaran, pengiriman/resi, laporan, pesan pelanggan, dan pengaturan toko.

## Akun demo
| Peran | Email | Kata sandi |
|---|---|---|
| Admin | admin@lumierebooks.local | LumiereAdmin123! |
| Pelanggan | user@lumierebooks.local | LumiereUser123! |

Password tersimpan sebagai hash. Pendaftaran publik hanya dapat membuat akun pelanggan. Untuk membuat administrator tambahan, masuk sebagai admin, buka menu **Data User**, lalu gunakan formulir **Tambah Administrator**. Pembuatan akun admin tidak tersedia untuk pengunjung umum. Alternatif melalui terminal: `php skrip/buat-admin.php admin@email.com 'PasswordMinimal10' 'Nama Admin'`.

## Instalasi XAMPP / Laragon
1. Ekstrak kedua folder `lumiere-books` dan `lumiere-storage` langsung ke `htdocs` (XAMPP), sehingga keduanya berada berdampingan. Untuk Laragon, letakkan keduanya berdampingan di folder web yang sama.
2. Jalankan Apache dan MySQL.
3. Buat database `lumiere_books` melalui phpMyAdmin, lalu impor `database/schema.sql`.
4. Salin `.env.example` menjadi `.env`, sesuaikan DB_HOST, DB_NAME, DB_USER, dan DB_PASS.
5. Buka `http://localhost/lumiere-books/public/`.
6. Login admin melalui ikon akun lalu gunakan akun demo admin di atas.

**Penyimpanan gambar permanen:** semua cover buku, gambar kategori, dan avatar hasil upload disimpan di folder `lumiere-storage` yang berada di samping `lumiere-books`, bukan di dalam folder kode website. Saat memasang ZIP pembaruan, ganti folder `lumiere-books` saja dan jangan menghapus/menimpa `lumiere-storage`. Detail ada di `PENTING-PENYIMPANAN-GAMBAR.txt`.

Pastikan PHP memiliki ekstensi `pdo_mysql` dan `fileinfo`.

## Simulasi pembayaran
Pembayaran BCA/BRI menampilkan nomor rekening/VA DEMO dan tombol simulasi berhasil/gagal. COD dibayar ketika paket diterima; admin dapat memperbarui status pembayaran. Tidak ada uang sungguhan yang diproses.

## Catatan
Status pengiriman dan nomor resi diinput admin. Pelacakan live memerlukan API resmi kurir, sehingga versi praktik ini menggunakan timeline status yang dikelola admin. Ganti kredensial demo sebelum penggunaan publik.
