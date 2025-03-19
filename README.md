# Sistem Peminjaman Barang Sederhana

Sistem ini memungkinkan pengelolaan peminjaman barang dengan dua role pengguna: **Admin** dan **User**. Dibangun menggunakan PHP, MySQL, HTML, dan CSS.

## Fitur

### Admin
- Kelola barang (tambah, edit, hapus).
- Kelola user (tambah, edit, hapus).
- Lihat dan konfirmasi peminjaman.

### User
- Lihat daftar barang.
- Ajukan peminjaman barang.
- Lihat riwayat peminjaman.

## Akun Default
- **Admin**:
  - Username: `admin`
  - Password: `admin`
- **User**:
  - Username: `user`
  - Password: `user`

## Instalasi

1. Clone repositori:
   ```bash
   git clone https://github.com/MrAnxious/Sistem-Peminjaman-Barang-Sederhana.git
   ```
2. Buat database dan import `database.sql`.
3. Sesuaikan koneksi database di `koneksi.php`.
4. Jalankan di server lokal (XAMPP/Laragon).

## Struktur Database
- **users**: Data user (username, password, role).
- **barang**: Data barang (nama, jumlah, deskripsi).
- **peminjaman**: Data peminjaman (id_user, id_barang, tanggal_pinjam, status).

## Lisensi
[MIT License](LICENSE).

---
Mr. Anxious
