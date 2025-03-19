<?php
require '../functions.php';
require '../koneksi.php';

// Cek apakah user sudah login dan memiliki role user
if (!isLoggedIn() || !isUser()) {
    redirect('../index.php');
}

// Ambil ID barang dari URL
if (!isset($_GET['id'])) {
    redirect('../user/dashboard.php');
}
$id = intval($_GET['id']); // Pastikan ID adalah integer

// Ambil data barang dari database
$query = "SELECT * FROM barang WHERE id = $id";
$result = $koneksi->query($query);

if ($result->num_rows === 0) {
    redirect('../user/dashboard.php'); // Redirect jika barang tidak ditemukan
}
$barang = $result->fetch_assoc();

// Proses form peminjaman
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah_pinjam = intval($_POST['jumlah_pinjam']); // Pastikan jumlah adalah integer
    $keterangan_peminjaman = trim($_POST['keterangan_peminjaman']); // Bersihkan input

    // Validasi jumlah pinjam
    if ($jumlah_pinjam <= 0) {
        echo "<script>alert('Jumlah pinjam harus lebih dari 0.');</script>";
    } elseif ($barang['jumlah'] < $jumlah_pinjam) {
        echo "<script>alert('Stok barang tidak mencukupi.');</script>";
    } else {
        // Insert data peminjaman ke database dengan status 'menunggu'
        $query = "INSERT INTO peminjaman (user_id, barang_id, jumlah_pinjam, tanggal_pinjam, keterangan_peminjaman, status_peminjaman) 
                  VALUES (?, ?, ?, ?, ?, 'menunggu')";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("iiiss", $_SESSION['user_id'], $id, $jumlah_pinjam, date('Y-m-d'), $keterangan_peminjaman);
        $stmt->execute();

        // Redirect ke dashboard setelah berhasil
        redirect('../user/dashboard.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Barang</title>
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
        <h1>Pinjam Barang</h1>
        <a href="../user/dashboard.php" class="btn">Kembali ke Dashboard</a>
        <form method="POST">
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" id="nama_barang" value="<?= htmlspecialchars($barang['nama_barang'] ?? '') ?>" readonly>
            </div>
            <div class="form-group">
                <label for="jumlah_pinjam">Jumlah Pinjam:</label>
                <input type="number" id="jumlah_pinjam" name="jumlah_pinjam" min="1" max="<?= htmlspecialchars($barang['jumlah']) ?>" required>
            </div>
            <div class="form-group">
                <label for="keterangan_peminjaman">Alasan Peminjaman:</label>
                <textarea id="keterangan_peminjaman" name="keterangan_peminjaman" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn">Ajukan Pinjaman</button>
        </form>
    </div>
</body>
</html>