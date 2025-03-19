<?php
require '../functions.php';
require '../koneksi.php';

// Cek apakah user sudah login dan memiliki role admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Ambil data user dari database
$query = "SELECT id, username, role FROM users";
$result = $koneksi->query($query);

// Handle error jika query gagal
if (!$result) {
    die("Query failed: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Member</title>
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
        <h1>Kelola Member</h1>
        <div class="actions">
            <a href="tambah_member.php" class="btn">Tambah Member</a>
            <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <a href="edit_member.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                                <a href="hapus_member.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus member ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data member.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>