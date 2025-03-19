<?php
require '../functions.php';
require '../koneksi.php';

// Cek apakah user sudah login dan memiliki role admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Pagination
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Halaman saat ini
$offset = ($page - 1) * $limit; // Hitung offset

// Ambil total data peminjaman
$query_total = "SELECT COUNT(*) as total FROM peminjaman";
$result_total = $koneksi->query($query_total);

if (!$result_total) {
    die("Query error: " . $koneksi->error); // Tampilkan pesan error jika query gagal
}

$total_data = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit); // Hitung total halaman

// Ambil data peminjaman dengan pagination
$query = "SELECT p.*, u.username, b.nama_barang 
          FROM peminjaman p
          JOIN users u ON p.user_id = u.id
          JOIN barang b ON p.barang_id = b.id
          LIMIT $limit OFFSET $offset";
$result = $koneksi->query($query);

if (!$result) {
    die("Query error: " . $koneksi->error); // Tampilkan pesan error jika query gagal
}

// Proses konfirmasi pengembalian
if (isset($_GET['kembalikan']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $tanggal_kembali = date('Y-m-d'); // Tanggal hari ini

    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // Ambil jumlah pinjam dan barang_id dari peminjaman
        $query_peminjaman = "SELECT barang_id, jumlah_pinjam FROM peminjaman WHERE id = ?";
        $stmt_peminjaman = $koneksi->prepare($query_peminjaman);
        $stmt_peminjaman->bind_param("i", $id);
        $stmt_peminjaman->execute();
        $result_peminjaman = $stmt_peminjaman->get_result();

        if ($result_peminjaman->num_rows === 0) {
            throw new Exception("Data peminjaman tidak ditemukan.");
        }

        $peminjaman = $result_peminjaman->fetch_assoc();
        $barang_id = $peminjaman['barang_id'];
        $jumlah_pinjam = $peminjaman['jumlah_pinjam'];

        // Update status pengembalian dan tanggal kembali
        $query_update = "UPDATE peminjaman 
                         SET status_pengembalian = 'dikembalikan', tanggal_kembali = ? 
                         WHERE id = ?";
        $stmt_update = $koneksi->prepare($query_update);
        $stmt_update->bind_param("si", $tanggal_kembali, $id);
        $stmt_update->execute();

        // Tambahkan stok barang
        $query_tambah_stok = "UPDATE barang 
                              SET jumlah = jumlah + ? 
                              WHERE id = ?";
        $stmt_tambah_stok = $koneksi->prepare($query_tambah_stok);
        $stmt_tambah_stok->bind_param("ii", $jumlah_pinjam, $barang_id);
        $stmt_tambah_stok->execute();

        // Commit transaksi
        $koneksi->commit();

        // Redirect untuk menghindari resubmission
        redirect('data_peminjam.php');
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        $koneksi->rollback();
        echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.location.href='data_peminjam.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peminjam</title>
    <link rel="stylesheet" href="../css/style_admin.css">
    <style>
        /* Sembunyikan elemen yang tidak diperlukan saat dicetak */
        @media print {
            .navbar, .action-buttons, .btn, .pagination {
                display: none;
            }

            /* Atur ukuran tabel untuk cetakan mode potrait */
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px; /* Ukuran font lebih kecil */
            }

            table, th, td {
                border: 1px solid black;
            }

            th, td {
                padding: 6px; /* Padding lebih kecil */
                text-align: left;
            }

            /* Ganti teks "Aksi" menjadi "Tanda Tangan" */
            th:nth-child(9)::after {
                content: "Tanda Tangan";
            }

            td:nth-child(9)::after {
                content: "";
            }

            /* Sembunyikan tombol aksi saat dicetak */
            td:nth-child(9) a {
                display: none;
            }
        }

        /* Tampilkan kolom "Aksi" saat tidak dicetak */
        @media screen {
            th:nth-child(9), td:nth-child(9) {
                display: table-cell;
            }
        }

        /* Gaya untuk tombol pagination */
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #0056b3;
        }
    </style>
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
        <h1>Data Peminjam</h1>
        <div class="action-buttons">
            <a href="../admin/dashboard.php" class="btn">Kembali ke Dashboard</a>
            <a href="javascript:void(0);" onclick="printReport()" class="btn">Cetak Laporan</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama Peminjam</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Pinjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Alasan Peminjaman</th>
                    <th>Status Peminjaman</th>
                    <th>Status Pengembalian</th>
                    <th>Aksi</th> <!-- Kolom ini akan diganti dengan "Tanda Tangan" saat dicetak -->
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['nama_barang'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['jumlah_pinjam'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pinjam'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['tanggal_kembali'] ?? 'Belum dikembalikan') ?></td>
                        <td><?= htmlspecialchars($row['keterangan_peminjaman'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['status_peminjaman'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['status_pengembalian'] ?? 'belum_dikembalikan') ?></td>
                        <td>
                            <?php if ($row['status_peminjaman'] === 'menunggu') : ?>
                            <a href="setujui_peminjaman.php?id=<?= $row['id'] ?>" class="btn btn-edit">Setujui</a>
                            <a href="tolak_peminjaman.php?id=<?= $row['id'] ?>" class="btn btn-danger">Tolak</a>
                            <?php endif; ?>
                            <?php if ($row['status_pengembalian'] === 'belum_dikembalikan' && $row['status_peminjaman'] === 'dipinjam') : ?>
                            <a href="data_peminjam.php?kembalikan=true&id=<?= $row['id'] ?>" class="btn btn-success">Konfirmasi Pengembalian</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">Tidak ada data peminjaman.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tombol Pagination -->
        <div class="pagination">
            <?php if ($page > 1) : ?>
                <a href="data_peminjam.php?page=<?= $page - 1 ?>">Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="data_peminjam.php?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages) : ?>
                <a href="data_peminjam.php?page=<?= $page + 1 ?>">Selanjutnya</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function printReport() {
            // Cetak laporan
            window.print();
        }
    </script>
</body>
</html>