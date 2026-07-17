<?php
// ============================================================
// FILE: index.php  (Halaman Login)
// Fungsi: Autentikasi admin ke dalam sistem SPMB
// ============================================================

session_start(); // Mulai sesi PHP

// Definisikan base URL agar link bisa berjalan dari direktori manapun
define('BASE_URL', '');

// Jika admin sudah login, langsung redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'includes/koneksi.php';

$error_msg = ''; // Variabel untuk menyimpan pesan error

// ===== PROSES LOGIN (saat form di-submit) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil & bersihkan input dari form
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi: field tidak boleh kosong
    if (empty($username) || empty($password)) {
        $error_msg = 'Username dan Password tidak boleh kosong!';
    } else {
        // Cari user berdasarkan username menggunakan prepared statement
        // (mencegah SQL Injection)
        $stmt = $koneksi->prepare("SELECT id, nama, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password dengan password_verify (password sudah di-hash)
            if (password_verify($password, $user['password'])) {
                // Login berhasil: simpan data admin ke session
                $_SESSION['admin_id']   = $user['id'];
                $_SESSION['admin_nama'] = $user['nama'];

                // Redirect ke dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error_msg = 'Password yang Anda masukkan salah!';
            }
        } else {
            $error_msg = 'Username tidak ditemukan!';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SPMB SMK KP Baros</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">

    <div class="login-card">
        <!-- Header kartu login -->
        <div class="login-header">
            <img src="assets/img/logo.jpg" alt="Logo SMK KP Baros" class="login-logo">
            <h5 class="text-white fw-bold mb-0 mt-2">SPMB SMK KP BAROS</h5>
            <p class="text-white-50 small mb-0">Sistem Penerimaan Murid Baru</p>
        </div>

        <!-- Body kartu login -->
        <div class="card-body p-4 bg-white">
            <h6 class="text-center text-muted mb-3">Masuk ke Panel Admin</h6>

            <!-- Tampilkan pesan error jika ada -->
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= htmlspecialchars($error_msg) ?>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Masukkan username"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                               required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-spmb-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>

            <p class="text-center text-muted small mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Demo: username <strong>admin</strong> / password <strong>admin123</strong>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
