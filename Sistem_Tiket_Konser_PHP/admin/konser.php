<?php
require '../config/database.php';
session_start();
require '../includes/auth_middleware.php';
check_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_konser'])) {
    $nama = $_POST['nama_konser'];
    $tgl = $_POST['tanggal'];
    $lok = $_POST['lokasi'];
    $iklan = isset($_POST['slot_iklan']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO konser (nama_konser, tanggal, lokasi, slot_iklan) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nama, $tgl, $lok, $iklan]);
    $konser_id = $pdo->lastInsertId();
    
    // Tambah Stok Default
    $stmtStok = $pdo->prepare("INSERT INTO tiket_stok (konser_id, kategori_tiket, harga, kuota, sisa_kuota) VALUES (?, 'Festival', 500000, 100, 100)");
    $stmtStok->execute([$konser_id]);
    
    header("Location: konser.php?msg=Konser Ditambahkan");
}

$konser = $pdo->query("SELECT * FROM konser")->fetchAll();
?>
<h2>Manajemen Konser</h2>
<form method="POST">
    <input type="text" name="nama_konser" placeholder="Nama Konser" required>
    <input type="datetime-local" name="tanggal" required>
    <input type="text" name="lokasi" placeholder="Lokasi" required>
    <label><input type="checkbox" name="slot_iklan"> Tampilkan di Rekomendasi</label>
    <button type="submit" name="add_konser">Tambah Konser</button>
</form>
<hr>
<table border="1">
    <tr><th>ID</th><th>Nama</th><th>Tanggal</th><th>Iklan</th></tr>
    <?php foreach($konser as $k): ?>
    <tr>
        <td><?= $k['id'] ?></td>
        <td><?= htmlspecialchars($k['nama_konser']) ?></td>
        <td><?= $k['tanggal'] ?></td>
        <td><?= $k['slot_iklan'] ? 'Ya' : 'Tidak' ?></td>
    </tr>
    <?php endforeach; ?>
</table>