<?php
require '../functions.php';
require '../koneksi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$query = "SELECT * FROM barang";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <h1>Daftar Barang</h1>
        <div class="actions">
            <a href="tambah_barang.php" class="btn">Tambah Barang</a>
            <a href="data_peminjam.php" class="btn">Lihat Data Peminjam</a>
            <a href="../admin/member.php" class="btn">Kelola user</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['nama_barang'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td><?= $row['keterangan'] ?></td>
                    <td>
                        <?php if ($row['gambar']) : ?>
                        <img src="../uploads/<?= $row['gambar'] ?>" alt="Gambar Barang" class="gambar-barang">
                        <?php else : ?>
                        <span class="no-image">Tidak ada gambar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_barang.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                        <a href="hapus_barang.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>