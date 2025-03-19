<?php
require '../functions.php';
require '../koneksi.php';

// Cek apakah user sudah login dan memiliki role admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Proses form tambah member
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Simpan password tanpa di-hash
    $role = $_POST['role'];

    // Insert data ke database
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sss", $username, $password, $role);
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
    <title>Tambah Member</title>
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
        <h1>Tambah Member</h1>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit" class="btn">Tambah</button>
            <a href="member.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>