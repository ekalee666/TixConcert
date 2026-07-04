<?php
require 'config/database.php';
include 'includes/header.php';

// 1. Ambil ID Konser dari URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// 2. Ambil Data Konser berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM konser WHERE id = ?");
$stmt->execute([$id]);
$k = $stmt->fetch();

if (!$k) {
    echo "<div class='container mt-5'><h3>Konser tidak ditemukan!</h3></div>";
    include 'includes/footer.php';
    exit;
}

// 3. Ambil Kategori Tiket & Stok (Mendefinisikan $stoks agar tidak error lagi)
$stmt_stok = $pdo->prepare("SELECT * FROM tiket_stok WHERE konser_id = ?");
$stmt_stok->execute([$id]);
$stoks = $stmt_stok->fetchAll();
?>

<div class="container mt-5">
    <div class="row">
        <!-- Kolom Kiri: Gambar dan Deskripsi -->
        <div class="col-lg-8">
            <img src="<?= $k['gambar'] ?>" class="img-fluid rounded-4 mb-4 shadow" style="width:100%; height:400px; object-fit:cover;">
            <h1 class="fw-bold"><?= htmlspecialchars($k['nama_konser']) ?></h1>
            <p class="text-muted fs-5">
                📍 <?= htmlspecialchars($k['lokasi']) ?> | 📅 <?= date('d F Y', strtotime($k['tanggal'])) ?>
            </p>
            <div class="card border-0 shadow-sm p-4 mt-4 mb-4">
                <h5 class="fw-bold">Deskripsi Acara</h5>
                <p class="text-secondary"><?= nl2br(htmlspecialchars($k['deskripsi'])) ?></p>
            </div>
        </div>
        
        <!-- Kolom Kanan: Pilihan Tiket -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg p-4 sticky-top" style="top: 100px; border-radius: 15px;">
                <h5 class="fw-bold mb-4">Pilih Kategori Tiket</h5>
                
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-warning small">
                        <strong>Ingin Beli?</strong><br>
                        Silakan <a href="login.php" class="fw-bold">Login</a> atau <a href="register.php" class="fw-bold">Daftar</a> terlebih dahulu.
                    </div>
                <?php endif; ?>

                <?php if (empty($stoks)): ?>
                    <p class="text-muted">Tiket belum tersedia untuk konser ini.</p>
                <?php else: ?>
                    <?php foreach($stoks as $s): 
                        $harga_final = $s['harga'] - ($s['diskon'] / 100 * $s['harga']);
                    ?>
                    <div class="border rounded-3 p-3 mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($s['kategori_tiket']) ?></h6>
                                <p class="text-success fw-bold mb-0">Rp<?= number_format($harga_final, 0, ',', '.') ?></p>
                                
                                <?php if($s['diskon'] > 0): ?>
                                    <small class="text-danger text-decoration-line-through">Rp<?= number_format($s['harga'], 0, ',', '.') ?></small>
                                    <span class="badge bg-danger ms-1">Diskon <?= $s['diskon'] ?>%</span>
                                <?php endif; ?>
                                
                                <div class="small text-muted mt-1">Sisa Stok: <?= $s['sisa_kuota'] ?></div>
                            </div>

                            <!-- Form untuk lanjut ke tahap Pembayaran -->
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <form action="pembayaran.php" method="POST">
                                    <input type="hidden" name="stok_id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-primary px-3" <?= $s['sisa_kuota'] <= 0 ? 'disabled' : '' ?>>
                                        <?= $s['sisa_kuota'] <= 0 ? 'Habis' : 'Pilih' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-sm btn-outline-primary">Pilih</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>