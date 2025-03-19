<?php
require '../functions.php';
require '../koneksi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$id = $_GET['id']; // ID peminjaman

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update status peminjaman dan tanggal kembali
    $query = "UPDATE peminjaman SET status = 'dikembalikan', tanggal_kembali = CURDATE() WHERE id = $id";
    $koneksi->query($query);

    // Tambahkan jumlah barang yang dikembalikan ke stok
    $query = "UPDATE barang b
              JOIN peminjaman p ON b.id = p.barang_id
              SET b.jumlah = b.jumlah + p.jumlah_pinjam
              WHERE p.id = $id";
    $koneksi->query($query);

    redirect('data_peminjam.php');
}

// Ambil data peminjaman
$query = "SELECT p.*, u.username, b.nama_barang 
          FROM peminjaman p
          JOIN users u ON p.user_id = u.id
          JOIN barang b ON p.barang_id = b.id
          WHERE p.id = $id";
$result = $koneksi->query($query);
$peminjaman = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pengembalian Barang</title>
    <link rel="stylesheet" href="../css/style_admin.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="../admin/dashboard.php" class="navbar-brand">Admin Dashboard</a>
        <div class="navbar-nav">
            <a href="../logout.php" class="btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1>Konfirmasi Pengembalian Barang</h1>
        <p>Anda akan mengonfirmasi pengembalian barang berikut:</p>
        <ul>
            <li><strong>Nama Peminjam:</strong> <?= $peminjaman['username'] ?></li>
            <li><strong>Nama Barang:</strong> <?= $peminjaman['nama_barang'] ?></li>
            <li><strong>Jumlah Pinjam:</strong> <?= $peminjaman['jumlah_pinjam'] ?></li>
            <li><strong>Tanggal Pinjam:</strong> <?= $peminjaman['tanggal_pinjam'] ?></li>
            <li><strong>Alasan Peminjaman:</strong> <?= $peminjaman['keterangan_peminjaman'] ?></li>
        </ul>
        <form method="POST">
            <button type="submit" class="btn">Konfirmasi Pengembalian</button>
            <a href="data_peminjam.php" class="btn btn-danger">Batal</a>
        </form>
    </div>
</body>
</html>