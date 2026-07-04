<?php
require '../config/database.php';
session_start();

// Proteksi Halaman: Hanya Admin yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil Statistik Sederhana
$total_user = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pembeli'")->fetchColumn();
$total_terjual = $pdo->query("SELECT COUNT(*) FROM tiket_terjual")->fetchColumn();
$total_pendapatan = $pdo->query("SELECT SUM(total_bayar) FROM transaksi WHERE status_pembayaran = 'success'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - TixConcert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-dark text-white p-4" style="width: 250px; min-height: 100vh;">
        <h4 class="fw-bold text-primary">Admin Panel</h4>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="dashboard.php" class="nav-link text-white">Dashboard</a></li>
            <li class="nav-item"><a href="validasi.php" class="nav-link text-white">Validasi Tiket</a></li>
            <li class="nav-item"><a href="../logout.php" class="nav-link text-danger mt-5">Logout</a></li>
            <!-- Cari bagian sidebar di dashboard.php dan tambahkan baris ini -->
            <li class="nav-item"><a href="tambah_konser.php" class="nav-link text-white">➕ Tambah Konser</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="p-5 w-100">
        <h3>Selamat Datang, Admin <?= $_SESSION['nama'] ?></h3>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white p-3 shadow-sm">
                    <h6>Total Pembeli</h6>
                    <h2><?= $total_user ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white p-3 shadow-sm">
                    <h6>Tiket Terjual</h6>
                    <h2><?= $total_terjual ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark p-3 shadow-sm">
                    <h6>Total Pendapatan</h6>
                    <h2>Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h4>Transaksi Terbaru</h4>
            <table class="table table-white table-striped shadow-sm">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Konser</th>
                        <th>Total Bayar</th>
                        <th>Metode</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT u.nama, k.nama_konser, t.total_bayar, t.metode_pembayaran, t.tanggal_transaksi 
                                         FROM transaksi t 
                                         JOIN users u ON t.user_id = u.id 
                                         JOIN konser k ON t.konser_id = k.id 
                                         ORDER BY t.tanggal_transaksi DESC LIMIT 10");
                    while($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['nama_konser'] ?></td>
                        <td>Rp<?= number_format($row['total_bayar'],0,',','.') ?></td>
                        <td><span class="badge bg-secondary"><?= $row['metode_pembayaran'] ?></span></td>
                        <td><?= $row['tanggal_transaksi'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>