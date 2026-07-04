<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TixConcert - Booking Tiket Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .navbar { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); }
        .hero-section { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1459749411177-042180ce673c?q=80&w=2000');
            background-size: cover; background-position: center; color: white; padding: 100px 0;
        }
        .card-concert { border: none; border-radius: 15px; overflow: hidden; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .card-concert:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .btn-primary { border-radius: 30px; padding: 10px 25px; font-weight: 600; }
        .promo-badge { position: absolute; top: 15px; left: 15px; background: #ffc107; color: black; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary fs-3" href="index.php">Tix<span class="text-dark">Concert</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Jelajahi</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="tiket_saya.php">Tiket Saya</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm ms-lg-3" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Masuk</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-3 text-white" href="register.php">Daftar Sekarang</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>