<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

function restrictToAdmin() {
    if (!isAdmin()) {
        header("Location: ../login.php?error=Akses ditolak");
        exit;
    }
}

function restrictToUser() {
    if (!isLoggedIn()) {
        header("Location: login.php?error=Silakan login terlebih dahulu");
        exit;
    }
}
?>