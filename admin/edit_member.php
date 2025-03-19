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

// Ambil data user dari database
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('member.php');
}
$user = $result->fetch_assoc();

// Proses form edit member
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Jika password diisi, hash password baru
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    // Update data di database
    $query = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sssi", $username, $password, $role, $id);
    $stmt->execute();

    // Redirect ke halaman member
    redirect('member.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
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
        <h1>Edit Member</h1>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= $user['username'] ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password (biarkan kosong jika tidak ingin mengubah):</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            <button type="submit" class="btn">Simpan</button>
            <a href="member.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>