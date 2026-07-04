<?php
require '../config/database.php';
session_start();
if ($_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Simpan Data Konser
        $stmt = $pdo->prepare("INSERT INTO konser (nama_konser, deskripsi, tanggal, lokasi, gambar, slot_iklan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nama_konser'],
            $_POST['deskripsi'],
            $_POST['tanggal'],
            $_POST['lokasi'],
            $_POST['gambar'],
            isset($_POST['slot_iklan']) ? 1 : 0
        ]);
        
        $konser_id = $pdo->lastInsertId();

        // 2. Simpan Data Tiket (Bisa banyak kategori sekaligus)
        $kategori = $_POST['kategori']; // Array
        $harga = $_POST['harga'];       // Array
        $stok = $_POST['stok'];         // Array
        $diskon = $_POST['diskon'];     // Array

        $stmt_tiket = $pdo->prepare("INSERT INTO tiket_stok (konser_id, kategori_tiket, harga, kuota, sisa_kuota, diskon) VALUES (?, ?, ?, ?, ?, ?)");
        
        for ($i = 0; $i < count($kategori); $i++) {
            $stmt_tiket->execute([
                $konser_id,
                $kategori[$i],
                $harga[$i],
                $stok[$i],
                $stok[$i], // sisa_kuota awal sama dengan kuota
                $diskon[$i]
            ]);
        }

        $pdo->commit();
        $pesan = "<div class='alert alert-success'>✅ Konser & Tiket Berhasil Ditambahkan!</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $pesan = "<div class='alert alert-danger'>❌ Gagal: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Konser Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Kembali</a>
            <div class="card shadow border-0 p-4">
                <h3 class="fw-bold mb-4">Tambah Konser & Tiket Baru</h3>
                <?= $pesan ?>
                
                <form method="POST">
                    <div class="row">
                        <!-- Informasi Konser -->
                        <div class="col-md-6 border-end">
                            <h5 class="text-primary mb-3">1. Informasi Konser</h5>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Konser</label>
                                <input type="text" name="nama_konser" class="form-control" placeholder="Contoh: Sheila on 7 Live" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Stadion GBK, Jakarta" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Tanggal & Waktu</label>
                                <input type="datetime-local" name="tanggal" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">URL Gambar Poster</label>
                                <input type="text" name="gambar" class="form-control" placeholder="https://link-gambar.com/foto.jpg" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="slot_iklan" class="form-check-input" id="promo">
                                <label class="form-check-label" for="promo">Masukkan ke Slot Promo/Rekomendasi</label>
                            </div>
                        </div>

                        <!-- Kategori Tiket -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">2. Kategori Tiket</h5>
                            <div id="ticket-container">
                                <div class="ticket-row border p-3 rounded mb-3 bg-white shadow-sm">
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <label class="small">Kategori</label>
                                            <input type="text" name="kategori[]" class="form-control form-control-sm" placeholder="VIP" required>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small">Harga (Rp)</label>
                                            <input type="number" name="harga[]" class="form-control form-control-sm" placeholder="1000000" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="small">Kuota</label>
                                            <input type="number" name="stok[]" class="form-control form-control-sm" placeholder="100" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="small">Diskon (%)</label>
                                            <input type="number" name="diskon[]" class="form-control form-control-sm" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="addTicketRow()" class="btn btn-outline-primary btn-sm mb-4">+ Tambah Kategori Lain</button>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary btn-lg w-100">SIMPAN KONSER & TIKET</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk menambah baris input kategori tiket secara dinamis
function addTicketRow() {
    const container = document.getElementById('ticket-container');
    const newRow = document.createElement('div');
    newRow.className = 'ticket-row border p-3 rounded mb-3 bg-white shadow-sm';
    newRow.innerHTML = `
        <div class="row text-end"><button type="button" onclick="this.parentElement.parentElement.remove()" class="btn-close small"></button></div>
        <div class="row">
            <div class="col-6 mb-2">
                <label class="small">Kategori</label>
                <input type="text" name="kategori[]" class="form-control form-control-sm" placeholder="Reguler" required>
            </div>
            <div class="col-6 mb-2">
                <label class="small">Harga (Rp)</label>
                <input type="number" name="harga[]" class="form-control form-control-sm" required>
            </div>
            <div class="col-6">
                <label class="small">Kuota</label>
                <input type="number" name="stok[]" class="form-control form-control-sm" required>
            </div>
            <div class="col-6">
                <label class="small">Diskon (%)</label>
                <input type="number" name="diskon[]" class="form-control form-control-sm" value="0">
            </div>
        </div>
    `;
    container.appendChild(newRow);
}
</script>
</body>
</html>