<?php
require 'config/database.php';
include 'includes/header.php';
$status = $_GET['status'];
?>

<div class="container mt-5 text-center">
    <?php if ($status == 'sukses'): 
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT tj.serial_number FROM tiket_terjual tj WHERE tj.transaksi_id = ?");
        $stmt->execute([$id]);
        $t = $stmt->fetch();
    ?>
        <!-- TAMPILAN SUKSES -->
        <div class="card shadow-lg border-0 p-5 mx-auto" style="max-width: 500px; border-radius: 20px;">
            <h1 class="display-3 text-success">✅</h1>
            <h2 class="fw-bold">Pembayaran Berhasil!</h2>
            <div class="bg-light p-4 rounded-4 my-3 border">
                <p class="small text-muted mb-1">KODE TIKET RAHASIA:</p>
                <h2 class="fw-bold text-primary"><?= $t['serial_number'] ?></h2>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $t['serial_number'] ?>" class="mt-2">
            </div>
            <a href="tiket_saya.php" class="btn btn-primary w-100">Lihat Tiket Saya</a>
        </div>

    <?php else: ?>
        <!-- TAMPILAN GAGAL -->
        <div class="card shadow-lg border-0 p-5 mx-auto" style="max-width: 500px; border-radius: 20px;">
            <h1 class="display-3 text-danger">❌</h1>
            <h2 class="fw-bold">Transaksi Gagal</h2>
            <p class="text-muted"><?= $_SESSION['error_msg'] ?></p>
            <hr>
            <a href="index.php" class="btn btn-primary mb-2">Coba Kembali</a>
            <button class="btn btn-outline-secondary" onclick="alert('Saldo Anda akan segera diperbarui!')">Top Up Saldo</button>
        </div>
    <?php unset($_SESSION['error_msg']); endif; ?>
</div>