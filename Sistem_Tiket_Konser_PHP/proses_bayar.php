<?php
require 'config/database.php';
session_start();

$user_id = $_SESSION['user_id'];
$stok_id = $_POST['stok_id'];
$total_bayar = $_POST['total_bayar'];
$metode = $_POST['metode'];

try {
    $pdo->beginTransaction();

    // Ambil saldo user saat ini
    $stmtUser = $pdo->prepare("SELECT saldo FROM users WHERE id = ? FOR UPDATE");
    $stmtUser->execute([$user_id]);
    $user = $stmtUser->fetch();

    // CEK SALDO: Jika saldo user lebih kecil dari harga tiket
    if ($user['saldo'] < $total_bayar) {
        throw new Exception("Saldo tidak mencukupi! Saldo Anda Rp" . number_format($user['saldo'],0,',','.'));
    }

    // Jika saldo cukup, lanjut potong saldo dan stok
    $pdo->prepare("UPDATE users SET saldo = saldo - ? WHERE id = ?")->execute([$total_bayar, $user_id]);
    $pdo->prepare("UPDATE tiket_stok SET sisa_kuota = sisa_kuota - 1 WHERE id = ?")->execute([$stok_id]);
    
    // Masukkan data transaksi
    $stmtK = $pdo->prepare("SELECT konser_id, kategori_tiket FROM tiket_stok WHERE id = ?");
    $stmtK->execute([$stok_id]);
    $stok_data = $stmtK->fetch();

    $insTrans = $pdo->prepare("INSERT INTO transaksi (user_id, konser_id, total_bayar, metode_pembayaran, status_pembayaran) VALUES (?, ?, ?, ?, 'success')");
    $insTrans->execute([$user_id, $stok_data['konser_id'], $total_bayar, $metode]);
    $transaksi_id = $pdo->lastInsertId();

    $serial = "TIX-" . strtoupper(bin2hex(random_bytes(4)));
    $pdo->prepare("INSERT INTO tiket_terjual (transaksi_id, kategori_tiket, serial_number) VALUES (?, ?, ?)")->execute([$transaksi_id, $stok_data['kategori_tiket'], $serial]);

    $pdo->commit();
    header("Location: hasil_transaksi.php?status=sukses&id=$transaksi_id");

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_msg'] = $e->getMessage(); // Simpan pesan error
    header("Location: hasil_transaksi.php?status=gagal");
}