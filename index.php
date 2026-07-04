<?php 
require 'config/database.php';
include 'includes/header.php'; 
?>

<header class="hero-section text-center">
    <div class="container">
        <h1 class="display-3 fw-bold">Rasakan Sensasi Konser Nyata</h1>
        <p class="lead">Cari tiket konser musisi favoritmu dengan mudah, aman, dan cepat.</p>
    </div>
</header>

<div class="container mt-5">
    <!-- PROMO SECTION -->
    <h3 class="fw-bold mb-4">✨ Promo Spesial</h3>
    <div class="row g-4 mb-5">
        <?php
        $stmt = $pdo->query("SELECT * FROM konser WHERE slot_iklan = 1 LIMIT 2");
        while ($row = $stmt->fetch()):
        ?>
        <div class="col-md-6">
            <a href="detail.php?id=<?= $row['id'] ?>" class="text-decoration-none text-dark">
                <div class="card card-concert bg-dark text-white border-0 h-100 shadow-lg">
                    <img src="<?= $row['gambar'] ?>" class="card-img opacity-50" style="height: 300px; object-fit: cover;">
                    <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                        <span class="badge bg-warning text-dark mb-2 w-25">REKOMENDASI</span>
                        <h2 class="card-title fw-bold"><?= $row['nama_konser'] ?></h2>
                        <p class="card-text"><i class="bi bi-geo-alt"></i> <?= $row['lokasi'] ?></p>
                    </div>
                </div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- SEMUA KONSER -->
    <h3 class="fw-bold mb-4">Semua Pertunjukan</h3>
    <div class="row g-4">
        <?php
        $stmt = $pdo->query("SELECT * FROM konser");
        while ($row = $stmt->fetch()):
        ?>
        <div class="col-md-4 col-sm-6">
            <div class="card card-concert h-100">
                <img src="<?= $row['gambar'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <p class="text-primary small mb-1 fw-bold"><?= date('d M Y', strtotime($row['tanggal'])) ?></p>
                    <h5 class="card-title fw-bold"><?= $row['nama_konser'] ?></h5>
                    <p class="text-muted small"><?= $row['lokasi'] ?></p>
                    <hr>
                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary w-100">Beli Tiket</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>