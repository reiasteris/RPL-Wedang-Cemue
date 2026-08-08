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

### Pelayan

Pelayan bertanggung jawab terhadap proses pemesanan pelanggan.

Fitur:

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
### Koki

Koki bertanggung jawab terhadap pemrosesan pesanan yang masuk dari Pelayan.

Fitur:

- Melihat antrian pesanan.
- Melihat detail pesanan.
- Melihat nomor meja.
- Melihat jumlah pelanggan.
- Memulai proses pesanan.
- Menyelesaikan pesanan.
- Membatalkan pesanan apabila tidak dapat diproses.
- Mengelola menu.
- Menambahkan menu.
- Mengubah menu.
- Menghapus menu.
- Mengelola stok menu.

```text
Menunggu
    ↓
Diproses
    ↓
Selesai
```
---
#### Pembatalan Pesanan
Pembatalan Pesanan

Apabila pesanan tidak dapat diproses, Koki dapat membatalkan pesanan.

Sistem akan:
- Mengubah status pesanan menjadi dibatalkan.
- Mengubah status item pesanan menjadi dibatalkan.
- Mengembalikan stok menu.
- Mengembalikan status meja menjadi tersedia.

### Kasir

Kasir bertanggung jawab terhadap proses pembayaran pelanggan.

Fitur:
- Melihat pesanan yang siap dibayar.
- Melihat detail pesanan.
- Melihat total pembayaran.
- Memilih metode pembayaran.
- Memvalidasi pembayaran.
- Melihat transaksi yang telah selesai.
- Melihat pendapatan hari ini.
- Melihat informasi transaksi.
