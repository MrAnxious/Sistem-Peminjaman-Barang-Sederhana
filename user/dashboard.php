<?php
require '../functions.php';
require '../koneksi.php';

// Cek apakah user sudah login dan memiliki role user
if (!isLoggedIn() || !isUser()) {
    redirect('../index.php');
}

// Ambil daftar barang dari database
$query_barang = "SELECT * FROM barang";
$result_barang = $koneksi->query($query_barang);

// Ambil daftar peminjaman user
$user_id = $_SESSION['user_id'];
$query_peminjaman = "SELECT p.*, b.nama_barang, b.gambar, b.keterangan 
                     FROM peminjaman p
                     JOIN barang b ON p.barang_id = b.id
                     WHERE p.user_id = $user_id";
$result_peminjaman = $koneksi->query($query_peminjaman);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/style_user.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="../user/dashboard.php" class="navbar-brand">User Dashboard</a>
        <div class="navbar-nav">
            <a href="../logout.php" class="btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1>Daftar Barang</h1>
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Gambar</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_barang->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_barang'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['jumlah'] ?? '') ?></td>
                    <td>
                        <?php if (!empty($row['gambar'])) : ?>
                        <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar Barang" style="max-width: 100px;">
                        <?php else : ?>
                        Tidak ada gambar
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                    <td>
                        <a href="pinjam_barang.php?id=<?= $row['id'] ?>" class="btn">Pinjam</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Barang yang Dipinjam</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah Pinjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Alasan Peminjaman</th>
                    <th>Status Peminjaman</th>
                    <th>Status Pengembalian</th>
                    <th>Tanggal Kembali</th>
                    <th>Gambar</th>
                    <th>Keterangan Barang</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($row = $result_peminjaman->fetch_assoc()) : ?>
    <tr>
        <td><?= htmlspecialchars($row['nama_barang'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['jumlah_pinjam'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['tanggal_pinjam'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['keterangan_peminjaman'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['status_peminjaman'] ?? '') ?></td>
        <!-- Kolom Status Pengembalian -->
        <td class="<?= ($row['status_pengembalian'] === 'dikembalikan') ? 'status-dikembalikan' : 'status-belum-dikembalikan' ?>">
            <?= htmlspecialchars($row['status_pengembalian'] ?? 'belum_dikembalikan') ?>
        </td>
        <td><?= htmlspecialchars($row['tanggal_kembali'] ?? 'Belum dikembalikan') ?></td>
        <td>
            <?php if (!empty($row['gambar'])) : ?>
            <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar Barang" style="max-width: 100px;">
            <?php else : ?>
            Tidak ada gambar
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
    </tr>
    <?php endwhile; ?>
</tbody>
        </table>
    </div>
</body>
</html>