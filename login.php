<?php
require 'config/database.php';
session_start();

$status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        
        $status = "sukses_" . $user['role'];
    } else {
        $status = "salah";
    }
}
include 'includes/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5 py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-lg p-4" style="border-radius: 20px;">
                <div class="card-body">
                    <h3 class="fw-bold text-center mb-4">Login</h3>
                    <form method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="admin@mail.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">MASUK</button>
                    </form>
                    <p class="text-center mt-3 small">Belum punya akun? <a href="register.php">Daftar</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
<?php if ($status == "sukses_admin"): ?>
    Swal.fire({ icon: 'success', title: 'Halo Admin!', text: 'Mengalihkan ke Dashboard...', showConfirmButton: false, timer: 2000 
    }).then(() => { window.location.href = "admin/dashboard.php"; });
<?php elseif ($status == "sukses_pembeli"): ?>
    Swal.fire({ icon: 'success', title: 'Login Berhasil', text: 'Selamat datang kembali!', showConfirmButton: false, timer: 2000 
    }).then(() => { window.location.href = "index.php"; });
<?php elseif ($status == "salah"): ?>
    Swal.fire({ icon: 'error', title: 'Gagal Masuk', text: 'Email atau Password salah!' });
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>