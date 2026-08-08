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

Alur utama:
```text
Menunggu
    ↓
Diproses
    ↓
Selesai
```
#### Pembatalan Pesanan
Pembatalan Pesanan

Apabila pesanan tidak dapat diproses, Koki dapat membatalkan pesanan.

Sistem akan:
- Mengubah status pesanan menjadi dibatalkan.
- Mengubah status item pesanan menjadi dibatalkan.
- Mengembalikan stok menu.
- Mengembalikan status meja menjadi tersedia.
---

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

Alur utama:
``` text
Pesanan Selesai
      ↓
Pesanan Siap Dibayar
      ↓
Pilih Metode Pembayaran
      ↓
Validasi Pembayaran
      ↓
Transaksi Selesai
      ↓
Pendapatan Dicatat
```

# 🔐 Hak Akses

PAK RESTO memiliki beberapa jenis pengguna dengan hak akses yang berbeda sesuai dengan tanggung jawab masing-masing.

| Role | Fungsi |
|------|--------|
| Pelayan | Mengelola meja dan membuat pesanan pelanggan |
| Koki | Memproses pesanan serta mengelola menu dan stok |
| Kasir | Menangani pembayaran dan transaksi |
| Pemilik | Memantau operasional dan informasi restoran |

Setiap pengguna diarahkan ke halaman sesuai dengan role yang dimiliki setelah melakukan login.

---

# 👥 Akun Demo

| Role | Username | Password |
|------|----------|----------|
| Pelayan | `pelayan` | `12345` |
| Koki | `koki` | `12345` |
| Kasir | `kasir` | `12345` |

---

# 🔄 Alur Operasional Restoran

```text
Pelayan
   ↓
Menentukan Jumlah Pelanggan
   ↓
Memilih Meja
   ↓
Memilih Menu
   ↓
Membuat Pesanan
   ↓
Pesanan Masuk ke Dapur
   ↓
Koki Memproses Pesanan
   ↓
Pesanan Selesai
   ↓
Kasir Memproses Pembayaran
   ↓
Transaksi Selesai
   ↓
Pendapatan Dicatat
```

---

# 📊 Status Data

## Status Pesanan

```text
menunggu
diproses
selesai
dibatalkan
```

## Status Item Pesanan

```text
menunggu
diproses
selesai
dibatalkan
```

## Status Meja

```text
tersedia
terisi
```

## Status Menu

```text
tersedia
habis
```

---

# 🗄️ Database

Data utama yang digunakan:

- Pegawai
- Meja
- Menu
- Pesanan
- Detail Pesanan
- Pembayaran

Relasi utama:

```text
Pegawai
   ↓
Pesanan
   ↓
Detail Pesanan
   ↓
Menu
   ↓
Pembayaran
```

---

# 🛠️ Teknologi

| Teknologi | Kegunaan |
|-----------|----------|
| PHP | Backend dan pemrosesan aplikasi |
| MySQL | Database |
| HTML | Struktur halaman |
| CSS | Tampilan antarmuka |
| JavaScript | Interaksi halaman |
| XAMPP | Server lokal |
| Apache | Web server |
| phpMyAdmin | Pengelolaan database |

---

# 📁 Struktur Project

```text
pak-resto/
│
├── auth/
│   ├── login.php
│   ├── authenticate.php
│   └── logout.php
│
├── config/
│   └── database.php
│
├── pelayan/
│   ├── index.php
│   └── ...
│
├── koki/
│   ├── index.php
│   ├── menu.php
│   ├── tambah_menu.php
│   ├── edit_menu.php
│   ├── hapus_menu.php
│   └── ...
│
├── kasir/
│   ├── index.php
│   └── ...
│
├── pemilik/
│   ├── index.php
│   └── ...
│
├── create_users.php
├── dashboard.php
├── index.php
└── README.md
```

---

# 💻 Instalasi dan Menjalankan Program

## Persyaratan

- XAMPP
- Apache
- MySQL
- PHP
- phpMyAdmin
- Web Browser

## 1. Clone Repository

```bash
git clone https://github.com/reiasteris/pak-resto.git
cd pak-resto
```

## 2. Pindahkan ke XAMPP

Letakkan project pada:

```text
C:\xampp\htdocs\pak-resto\
```

## 3. Jalankan XAMPP

Aktifkan:

```text
Apache
MySQL
```

## 4. Buat Database

Buka:

```text
http://localhost/phpmyadmin
```

Buat database:

```text
pak_resto
```

Kemudian import database yang digunakan oleh project.

## 5. Konfigurasi Database

Buka:

```text
config/database.php
```

Contoh konfigurasi:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "pak_resto";
```

## 6. Jalankan Program

Buka:

```text
http://localhost/pak-resto/
```

---

# 🧪 Demo Program

## Video 1 — Pelayan

```text
Login
  ↓
Dashboard Pelayan
  ↓
Jumlah Pelanggan
  ↓
Pilih Meja
  ↓
Pilih Menu
  ↓
Review Pesanan
  ↓
Kirim Pesanan
```

Durasi: `< 5 menit`

## Video 2 — Koki

```text
Login
  ↓
Antrian Pesanan
  ↓
Melihat Detail Pesanan
  ↓
Mulai Proses
  ↓
Pesanan Selesai
```

Fungsi tambahan:

- Membatalkan pesanan.
- Mengembalikan stok menu.
- Mengembalikan status meja.
- Mengelola menu.
- Menambah menu.
- Mengubah menu.
- Menghapus menu.

Durasi: `< 5 menit`

## Video 3 — Kasir

```text
Login
  ↓
Dashboard Kasir
  ↓
Pesanan Siap Dibayar
  ↓
Melihat Detail Pesanan
  ↓
Memilih Metode Pembayaran
  ↓
Validasi Pembayaran
  ↓
Transaksi Selesai
  ↓
Melihat Pendapatan Hari Ini
```

Durasi: `< 5 menit`

---

# 🎯 Tujuan Project

PAK RESTO dibuat untuk membantu mengintegrasikan proses operasional restoran berdasarkan tanggung jawab setiap pengguna.

```text
Pengelolaan Meja
       ↓
Pembuatan Pesanan
       ↓
Pemrosesan Dapur
       ↓
Penyelesaian Pesanan
       ↓
Pembayaran
       ↓
Pencatatan Transaksi
       ↓
Informasi Pendapatan
```

---

# 📌 Catatan

Project ini dibuat sebagai bagian dari tugas/proyek akademik dan digunakan untuk demonstrasi sistem manajemen operasional restoran.

Akun demo yang tercantum pada README digunakan untuk kebutuhan pengujian aplikasi.
