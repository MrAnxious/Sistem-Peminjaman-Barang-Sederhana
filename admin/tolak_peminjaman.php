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
    // Update status peminjaman menjadi 'ditolak' dan status pengembalian menjadi 'dikembalikan'
    $query_update = "UPDATE peminjaman 
                     SET status_peminjaman = 'ditolak', 
                         status_pengembalian = 'dikembalikan' 
                     WHERE id = ?";
    $stmt_update = $koneksi->prepare($query_update);
    $stmt_update->bind_param("i", $id);
    $stmt_update->execute();

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