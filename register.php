<?php
require 'config/database.php';
include 'includes/header.php';

$status = ""; // Variabel untuk menampung status pendaftaran

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 1. Cek apakah email sudah ada (mencegah crash duplicate entry)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $emailTerdaftar = $stmt->fetchColumn();

    if ($emailTerdaftar > 0) {
        $status = "email_ada"; // Set status jika email sudah ada
    } else {
        // 2. Jika email belum ada, baru simpan ke database
        $ins = $pdo->prepare("INSERT INTO users (nama, email, password, role, saldo) VALUES (?, ?, ?, 'pembeli', 1000000)");
        if ($ins->execute([$nama, $email, $password])) {
            $status = "sukses";
        } else {
            $status = "error";
        }
    }
}
?>

<!-- Tambahkan Library SweetAlert2 dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5 py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-lg p-4" style="border-radius: 20px;">
                <div class="card-body">
                    <h3 class="fw-bold text-center mb-4">Daftar Akun</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">DAFTAR SEKARANG</button>
                    </form>
                    <p class="text-center mt-3 small">Sudah punya akun? <a href="login.php">Masuk</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logika Javascript untuk Menampilkan Pop-up Berdasarkan Status PHP -->
<script>
<?php if ($status == "email_ada"): ?>
    Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: 'Email ini sudah terdaftar! Silakan gunakan email lain atau langsung login.',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Tutup'
    });
<?php elseif ($status == "sukses"): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Akun Anda berhasil dibuat. Saldo awal Rp1.000.000 telah ditambahkan!',
        showConfirmButton: false,
        timer: 3000
    }).then(function() {
        window.location.href = "login.php";
    });
<?php elseif ($status == "error"): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Terjadi kesalahan sistem, silakan coba lagi nanti.',
    });
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>