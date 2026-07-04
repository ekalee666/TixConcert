<?php
require '../config/database.php';
session_start();
if ($_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$pesan = "";
if (isset($_POST['cek_tiket'])) {
    $sn = $_POST['serial_number'];
    $stmt = $pdo->prepare("SELECT tj.*, k.nama_konser, u.nama 
                           FROM tiket_terjual tj 
                           JOIN transaksi t ON tj.transaksi_id = t.id 
                           JOIN konser k ON t.konser_id = k.id 
                           JOIN users u ON t.user_id = u.id 
                           WHERE tj.serial_number = ?");
    $stmt->execute([$sn]);
    $tiket = $stmt->fetch();

    if ($tiket) {
        if ($tiket['status_penukaran'] == 'sudah') {
            $pesan = "<div class='alert alert-danger'>❌ TIKET SUDAH DIGUNAKAN pada penukaran sebelumnya!</div>";
        } else {
            // Update status penukaran
            $upd = $pdo->prepare("UPDATE tiket_terjual SET status_penukaran = 'sudah' WHERE serial_number = ?");
            $upd->execute([$sn]);
            $pesan = "
            <div class='alert alert-success'>
                <h4>✅ TIKET VALID</h4>
                <p>Pemilik: <strong>{$tiket['nama']}</strong><br>
                Konser: <strong>{$tiket['nama_konser']}</strong><br>
                Kategori: <strong>{$tiket['kategori_tiket']}</strong></p>
                <p class='mb-0 text-dark fw-bold'>STATUS: Berhasil Ditukarkan!</p>
            </div>";
        }
    } else {
        $pesan = "<div class='alert alert-warning'>⚠️ Kode Tiket TIDAK DITEMUKAN!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi Tiket - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Kembali ke Dashboard</a>
                <div class="card shadow p-4 text-center">
                    <h3 class="fw-bold mb-4">Validasi Tiket Venue</h3>
                    <form method="POST">
                        <input type="text" name="serial_number" class="form-control form-control-lg mb-3 text-center" placeholder="Masukkan Serial Number (TIX-XXXX)" required>
                        <button name="cek_tiket" class="btn btn-primary btn-lg w-100">CEK VALIDITAS</button>
                    </form>
                    <div class="mt-4 text-start">
                        <?= $pesan ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>