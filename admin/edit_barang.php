<?php
require '../functions.php';
require '../koneksi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$id = $_GET['id'];
$query = "SELECT * FROM barang WHERE id = $id";
$result = $koneksi->query($query);
$barang = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $gambarLama = $barang['gambar'];

    if ($_FILES['gambar']['error'] === 4) {
        $gambar = $gambarLama; // Gunakan gambar lama
    } else {
        $gambar = uploadGambar(); // Upload gambar baru
        if ($gambar) {
            // Hapus gambar lama jika ada
            if ($gambarLama && file_exists("../uploads/$gambarLama")) {
                unlink("../uploads/$gambarLama");
            }
        } else {
            echo "<script>alert('Gagal mengupload gambar.');</script>";
            $gambar = $gambarLama; // Gunakan gambar lama jika upload gagal
        }
    }

    // Update data barang
    $query = "UPDATE barang SET nama_barang = '$nama_barang', jumlah = $jumlah, keterangan = '$keterangan', gambar = '$gambar' WHERE id = $id";
    if ($koneksi->query($query)) {
        redirect('dashboard.php');
    } else {
        echo "<script>alert('Gagal mengupdate data barang.');</script>";
    }
}

function uploadGambar() {
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    // Cek apakah ada gambar yang diupload
    if ($error === 4) {
        return null; // Tidak ada gambar yang diupload
    }

    // Validasi ekstensi file
    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Format gambar tidak valid. Gunakan format JPG, JPEG, atau PNG.');</script>";
        return false;
    }

    // Validasi ukuran file (max 2MB)
    if ($ukuranFile > 2000000) {
        echo "<script>alert('Ukuran gambar terlalu besar. Maksimal 2MB.');</script>";
        return false;
    }

    // Generate nama file baru
    $namaFileBaru = uniqid() . '.' . $ekstensiFile;

    // Pindahkan file ke folder uploads
    if (move_uploaded_file($tmpName, '../uploads/' . $namaFileBaru)) {
        return $namaFileBaru;
    } else {
        echo "<script>alert('Gagal memindahkan gambar ke folder uploads.');</script>";
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
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
        <h1>Edit Barang</h1>
        <a href="../admin/dashboard.php" class="btn">Kembali ke Dashboard</a>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" name="nama_barang" value="<?= $barang['nama_barang'] ?>" required>
            </div>
            <div class="form-group">
                <label for="jumlah">Jumlah:</label>
                <input type="number" name="jumlah" value="<?= $barang['jumlah'] ?>" required>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan:</label>
                <textarea name="keterangan" rows="4" required><?= $barang['keterangan'] ?></textarea>
            </div>
            <div class="form-group">
    <label for="gambar">Gambar Barang:</label>
    <input type="file" name="gambar" accept="image/*">
    <small>Biarkan kosong jika tidak ingin mengubah gambar.</small>
    <?php if ($barang['gambar']) : ?>
    <img src="../uploads/<?= $barang['gambar'] ?>" alt="Gambar Barang" style="max-width: 200px; margin-top: 10px;">
    <?php endif; ?>
</div>
            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>
</body>
</html>