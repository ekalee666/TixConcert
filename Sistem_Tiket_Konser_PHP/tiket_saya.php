<?php
require 'config/database.php';
include 'includes/auth.php';
restrictToUser();
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT t.*, k.nama_konser, k.tanggal, k.lokasi, tj.serial_number, tj.status_penukaran 
                       FROM transaksi t
                       JOIN konser k ON t.konser_id = k.id
                       JOIN tiket_terjual tj ON t.id = tj.transaksi_id
                       WHERE t.user_id = ?");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h3>Tiket Saya</h3>
    <div class="row">
        <?php foreach ($tickets as $t): ?>
        <div class="col-md-6 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title"><?= $t['nama_konser'] ?></h5>
                    <p class="mb-1 text-muted"><?= $t['tanggal'] ?> | <?= $t['lokasi'] ?></p>
                    <div class="bg-light p-3 text-center border">
                        <small>Serial Number:</small>
                        <h4 class="fw-bold text-primary"><?= $t['serial_number'] ?></h4>
                        <span class="badge bg-<?= $t['status_penukaran'] == 'belum' ? 'warning' : 'success' ?>">
                            <?= strtoupper($t['status_penukaran']) ?> DITUKARKAN
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>