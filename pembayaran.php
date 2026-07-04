<?php
require 'config/database.php';
include 'includes/header.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$stok_id = $_POST['stok_id'];
$stmt = $pdo->prepare("SELECT k.nama_konser, s.* FROM tiket_stok s JOIN konser k ON s.konser_id = k.id WHERE s.id = ?");
$stmt->execute([$stok_id]);
$data = $stmt->fetch();

$total_bayar = $data['harga'] - ($data['diskon']/100 * $data['harga']);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0 p-4">
                <h4 class="fw-bold">Konfirmasi Pesanan</h4>
                <hr>
                <p>Konser: <strong><?= $data['nama_konser'] ?></strong></p>
                <p>Kategori: <span class="badge bg-info"><?= $data['kategori_tiket'] ?></span></p>
                <h5 class="text-success fw-bold">Total: Rp<?= number_format($total_bayar, 0, ',', '.') ?></h5>
                
                <form action="proses_bayar.php" method="POST" class="mt-4">
                    <input type="hidden" name="stok_id" value="<?= $stok_id ?>">
                    <input type="hidden" name="total_bayar" value="<?= $total_bayar ?>">
                    
                    <label class="small fw-bold">METODE PEMBAYARAN:</label>
                    <select name="metode" class="form-select mb-3" required>
                        <option value="">-- Pilih Pembayaran --</option>
                        <option value="bank">Transfer Bank (BCA/Mandiri)</option>
                        <option value="e-wallet">E-Wallet (OVO/Dana/GoPay)</option>
                        <option value="qris">QRIS (Scan Langsung)</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary w-100 py-3">KONFIRMASI BAYAR</button>
                </form>
            </div>
        </div>
    </div>
</div>