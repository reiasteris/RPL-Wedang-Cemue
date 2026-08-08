# PAK RESTO — Sistem Manajemen Operasional Restoran

Sistem informasi manajemen operasional restoran berbasis web yang dibuat untuk membantu mengintegrasikan proses pelayanan restoran, mulai dari pembuatan pesanan oleh pelayan, pengolahan pesanan oleh koki, hingga proses pembayaran dan pencatatan pendapatan oleh kasir.

Aplikasi ini menggunakan PHP sebagai backend, MySQL sebagai database, serta HTML, CSS, dan JavaScript untuk membangun antarmuka dan interaksi sistem.

---

## Gambaran Umum

PAK RESTO dirancang untuk mengintegrasikan beberapa bagian utama dalam operasional restoran ke dalam satu sistem.

Sistem memiliki beberapa role pengguna dengan hak akses yang berbeda:

- Pelayan
- Koki
- Kasir
- Pemilik

Setiap role memiliki halaman dan fungsi yang disesuaikan dengan proses kerja masing-masing.

---

## Fitur Sistem

### Portal Pelayan

Pelayan bertanggung jawab terhadap proses pemesanan pelanggan.

Fitur yang tersedia:

- Melihat kondisi dan ketersediaan meja.
- Menentukan jumlah pelanggan.
- Memilih meja berdasarkan kapasitas.
- Membuat pesanan pelanggan.
- Memilih makanan dan minuman.
- Menentukan jumlah setiap menu.
- Melihat ringkasan pesanan sebelum dikirim.
- Mengirim pesanan ke dapur.
- Melihat status pesanan.
- Melihat informasi meja dan pesanan yang sedang berjalan.

Alur utama:

```text
Jumlah Pelanggan
       ↓
Pilih Meja
       ↓
Pilih Menu
       ↓
Review Pesanan
       ↓
Kirim Pesanan
       ↓
Pesanan Masuk ke Dapur

```
---
