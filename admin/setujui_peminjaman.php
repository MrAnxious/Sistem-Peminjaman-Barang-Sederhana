<?php
require '../functions.php';
require '../koneksi.php';

// Cek apakah user sudah login dan memiliki role admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Ambil ID peminjaman dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('../admin/data_peminjam.php');
}
$id = intval($_GET['id']); // Pastikan ID adalah integer

// Mulai transaksi
$koneksi->begin_transaction();

try {
    // Ambil data peminjaman
    $query_peminjaman = "SELECT barang_id, jumlah_pinjam FROM peminjaman WHERE id = ?";
    $stmt_peminjaman = $koneksi->prepare($query_peminjaman);
    $stmt_peminjaman->bind_param("i", $id);
    $stmt_peminjaman->execute();
    $result_peminjaman = $stmt_peminjaman->get_result();

    if ($result_peminjaman->num_rows === 0) {
        throw new Exception("Data peminjaman tidak ditemukan.");
    }

    $peminjaman = $result_peminjaman->fetch_assoc();
    $barang_id = $peminjaman['barang_id'];
    $jumlah_pinjam = $peminjaman['jumlah_pinjam'];

    // Update status peminjaman menjadi 'dipinjam'
    $query_update = "UPDATE peminjaman 
                     SET status_peminjaman = 'dipinjam' 
                     WHERE id = ?";
    $stmt_update = $koneksi->prepare($query_update);
    $stmt_update->bind_param("i", $id);
    $stmt_update->execute();

    // Kurangi stok barang
    $query_kurangi_stok = "UPDATE barang 
                           SET jumlah = jumlah - ? 
                           WHERE id = ?";
    $stmt_kurangi_stok = $koneksi->prepare($query_kurangi_stok);
    $stmt_kurangi_stok->bind_param("ii", $jumlah_pinjam, $barang_id);
    $stmt_kurangi_stok->execute();

    // Commit transaksi
    $koneksi->commit();

    // Redirect ke halaman data peminjaman
    redirect('../admin/data_peminjam.php');
} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $koneksi->rollback();
    echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.location.href='../admin/data_peminjam.php';</script>";
    exit();
}
?>