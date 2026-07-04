<?php
require 'config/database.php';
require 'includes/auth.php';
restrictToUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $konser_id = $_POST['konser_id'];
    $stok_id = $_POST['stok_id']; // ID dari tabel tiket_stok
    $jumlah = 1; // Contoh beli 1

    try {
        $pdo->beginTransaction();

        // 1. Cek stok (Penting: Gunakan FOR UPDATE agar baris dikunci sementara)
        $stmt = $pdo->prepare("SELECT kategori_tiket, harga, sisa_kuota FROM tiket_stok WHERE id = ? FOR UPDATE");
        $stmt->execute([$stok_id]);
        $stok = $stmt->fetch();

        if (!$stok || $stok['sisa_kuota'] < $jumlah) {
            throw new Exception("Maaf, tiket kategori ini sudah habis!");
        }

        // 2. Kurangi stok
        $updateStok = $pdo->prepare("UPDATE tiket_stok SET sisa_kuota = sisa_kuota - ? WHERE id = ?");
        $updateStok->execute([$jumlah, $stok_id]);

        // 3. Simpan Transaksi
        $total_bayar = $stok['harga'] * $jumlah;
        $insTrans = $pdo->prepare("INSERT INTO transaksi (user_id, konser_id, total_bayar, metode_pembayaran) VALUES (?, ?, ?, 'bank')");
        $insTrans->execute([$user_id, $konser_id, $total_bayar]);
        $transaksi_id = $pdo->lastInsertId();

        // 4. Generate Tiket Terjual & Serial Number
        $serial = "TKT-" . strtoupper(bin2hex(random_bytes(4)));
        $insTiket = $pdo->prepare("INSERT INTO tiket_terjual (transaksi_id, kategori_tiket, serial_number, qr_code_path) VALUES (?, ?, ?, ?)");
        $insTiket->execute([$transaksi_id, $stok['kategori_tiket'], $serial, "qrcodes/$serial.png"]);

        $pdo->commit();
        header("Location: tiket_saya.php?success=Pembelian Berhasil!");

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Gagal: " . $e->getMessage();
    }
}
?>