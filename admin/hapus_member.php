<?php
require '../functions.php';
require '../koneksi.php';

// Cek apakah user sudah login dan memiliki role admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Ambil ID user dari URL
if (!isset($_GET['id'])) {
    redirect('member.php');
}
$id = intval($_GET['id']);

// Hapus data user dari database
$query = "DELETE FROM users WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirect ke halaman member
redirect('member.php');
?>