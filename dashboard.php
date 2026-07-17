<?php
// ============================================================
// FILE: dashboard.php
// Fungsi: Halaman utama admin — menampilkan ringkasan statistik
// ============================================================

session_start();
define('BASE_URL', '');

require_once 'includes/koneksi.php';

$page_title = 'Dashboard';

// ===== AMBIL DATA STATISTIK DARI DATABASE =====

// Hitung total seluruh pendaftar
$total_all = $koneksi->query("SELECT COUNT(*) AS total FROM calon_siswa")->fetch_assoc()['total'];

// Hitung per gelombang
$gel1 = $koneksi->query("SELECT COUNT(*) AS total FROM calon_siswa WHERE gelombang='Gelombang 1'")->fetch_assoc()['total'];
$gel2 = $koneksi->query("SELECT COUNT(*) AS total FROM calon_siswa WHERE gelombang='Gelombang 2'")->fetch_assoc()['total'];
$gel3 = $koneksi->query("SELECT COUNT(*) AS total FROM calon_siswa WHERE gelombang='Gelombang 3'")->fetch_assoc()['total'];

// Hitung per status
$diterima = $koneksi->query("SELECT COUNT(*) AS total FROM calon_siswa WHERE status='Diterima'")->fetch_assoc()['total'];
$pending  = $koneksi->query("SELECT COUNT(*) AS total FROM calon_siswa WHERE status='Pending'")->fetch_assoc()['total'];
$ditolak  = $koneksi->query("SELECT COUNT(*) AS total FROM calon_siswa WHERE status='Ditolak'")->fetch_assoc()['total'];

// Ambil 5 pendaftar terbaru untuk tabel ringkasan
$pendaftar_baru = $koneksi->query("SELECT * FROM calon_siswa ORDER BY created_at DESC LIMIT 5");

require_once 'includes/header.php';
?>

<!-- ===== JUDUL HALAMAN ===== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">
        <span class="page-title-bar"></span>
        Dashboard
    </h1>
    <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
</div>

<!-- ===== KARTU STATISTIK BARIS 1: Total & Per Gelombang ===== -->
<div class="row g-3 mb-4">
    <!-- Total Pendaftar -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-total p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 small opacity-75">Total Pendaftar</p>
                    <h2 class="fw-bold mb-0"><?= $total_all ?></h2>
                </div>
                <i class="bi bi-people-fill card-icon"></i>
            </div>
        </div>
    </div>
    <!-- Gelombang 1 -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-gel1 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 small opacity-75">Gelombang 1</p>
                    <h2 class="fw-bold mb-0"><?= $gel1 ?></h2>
                </div>
                <i class="bi bi-1-circle-fill card-icon"></i>
            </div>
        </div>
    </div>
    <!-- Gelombang 2 -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-gel2 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 small opacity-75">Gelombang 2</p>
                    <h2 class="fw-bold mb-0"><?= $gel2 ?></h2>
                </div>
                <i class="bi bi-2-circle-fill card-icon"></i>
            </div>
        </div>
    </div>
    <!-- Gelombang 3 -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-gel3 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 small opacity-75">Gelombang 3</p>
                    <h2 class="fw-bold mb-0"><?= $gel3 ?></h2>
                </div>
                <i class="bi bi-3-circle-fill card-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- ===== KARTU STATISTIK BARIS 2: Status ===== -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card stat-card stat-card-terima p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 small opacity-75">Diterima</p>
                    <h2 class="fw-bold mb-0"><?= $diterima ?></h2>
                </div>
                <i class="bi bi-check-circle-fill card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card stat-card stat-card-pending p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 small opacity-75">Pending</p>
                    <h2 class="fw-bold mb-0"><?= $pending ?></h2>
                </div>
                <i class="bi bi-hourglass-split card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card stat-card p-3" style="background:linear-gradient(135deg,#E63946,#FF6B6B);color:white;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 small opacity-75">Ditolak</p>
                    <h2 class="fw-bold mb-0"><?= $ditolak ?></h2>
                </div>
                <i class="bi bi-x-circle-fill card-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- ===== TABEL: 5 PENDAFTAR TERBARU ===== -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-clock-history text-primary me-2"></i>5 Pendaftar Terbaru
        </h6>
        <a href="pages/daftar_siswa.php" class="btn btn-sm btn-spmb-primary">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 tabel-siswa">
                <thead>
                    <tr>
                        <th class="ps-3">No</th>
                        <th>NISN</th>
                        <th>Nama Lengkap</th>
                        <th>Asal Sekolah</th>
                        <th>Gelombang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pendaftar_baru->num_rows > 0):
                        $no = 1;
                        while ($row = $pendaftar_baru->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-3"><?= $no++ ?></td>
                        <td><code><?= htmlspecialchars($row['nisn']) ?></code></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($row['asal_sekolah']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['gelombang']) ?></span></td>
                        <td>
                            <?php
                            $status_class = match($row['status']) {
                                'Diterima' => 'badge-diterima',
                                'Ditolak'  => 'badge-ditolak',
                                default    => 'badge-pending',
                            };
                            ?>
                            <span class="badge <?= $status_class ?>"><?= $row['status'] ?></span>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada data pendaftar.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
