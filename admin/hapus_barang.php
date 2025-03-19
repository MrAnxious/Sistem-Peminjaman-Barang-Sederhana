<?php
require '../functions.php';
require '../koneksi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$id = $_GET['id'];

// Ambil data barang untuk menghapus gambar
$query = "SELECT gambar FROM barang WHERE id = $id";
$result = $koneksi->query($query);
$barang = $result->fetch_assoc();

// Hapus gambar dari folder uploads
if ($barang['gambar'] && file_exists("../uploads/{$barang['gambar']}")) {
    unlink("../uploads/{$barang['gambar']}");
}

// Hapus data barang dari database
$query = "DELETE FROM barang WHERE id = $id";
$koneksi->query($query);

redirect('dashboard.php');
?>