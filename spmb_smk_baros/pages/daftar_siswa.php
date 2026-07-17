<?php
// ============================================================
// FILE: pages/daftar_siswa.php
// Fungsi: Menampilkan seluruh daftar pendaftar + fitur pencarian
// ============================================================

session_start();
define('BASE_URL', '../');

require_once '../includes/koneksi.php';

$page_title = 'Daftar Pendaftar';

// ===== LOGIKA PENCARIAN =====
// Ambil keyword dari URL jika ada (misal: ?cari=Ahmad)
$keyword = trim($_GET['cari'] ?? '');

if (!empty($keyword)) {
    // Jika ada keyword: query dengan LIKE untuk mencari di nama atau NISN
    $stmt = $koneksi->prepare("SELECT * FROM calon_siswa WHERE nama_lengkap LIKE ? OR nisn LIKE ? ORDER BY created_at DESC");
    $like_keyword = "%" . $keyword . "%";
    $stmt->bind_param("ss", $like_keyword, $like_keyword);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Jika tidak ada keyword: tampilkan semua
    $result = $koneksi->query("SELECT * FROM calon_siswa ORDER BY created_at DESC");
}

// ===== AMBIL PESAN NOTIFIKASI (dari redirect halaman lain) =====
$notif_msg  = $_SESSION['notif_msg']  ?? '';
$notif_type = $_SESSION['notif_type'] ?? 'success';
// Hapus setelah diambil agar tidak muncul lagi saat refresh
unset($_SESSION['notif_msg'], $_SESSION['notif_type']);

require_once '../includes/header.php';
?>

<!-- ===== NOTIFIKASI ===== -->
<?php if (!empty($notif_msg)): ?>
<div class="alert alert-<?= $notif_type ?> alert-dismissible fade show alert-auto-dismiss" role="alert">
    <i class="bi bi-<?= $notif_type === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
    <?= htmlspecialchars($notif_msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ===== JUDUL + TOMBOL TAMBAH ===== -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">
        <span class="page-title-bar"></span>
        Daftar Pendaftar
    </h1>
    <a href="tambah_siswa.php" class="btn btn-spmb-primary">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pendaftar
    </a>
</div>

<!-- ===== FORM PENCARIAN ===== -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="" class="d-flex gap-2 align-items-center">
            <input type="text" name="cari" class="form-control form-control-sm"
                   placeholder="Cari berdasarkan Nama atau NISN..."
                   value="<?= htmlspecialchars($keyword) ?>" style="max-width:320px;">
            <button type="submit" class="btn btn-sm btn-spmb-primary">
                <i class="bi bi-search me-1"></i> Cari
            </button>
            <?php if (!empty($keyword)): ?>
            <a href="daftar_siswa.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- ===== TABEL DATA PENDAFTAR ===== -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-2 px-3">
        <span class="small text-muted">
            <?php if (!empty($keyword)): ?>
                Menampilkan hasil pencarian: <strong>"<?= htmlspecialchars($keyword) ?>"</strong>
                (<?= $result->num_rows ?> ditemukan)
            <?php else: ?>
                Total: <strong><?= $result->num_rows ?> pendaftar</strong>
            <?php endif; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0 tabel-siswa">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:40px;">No</th>
                        <th>NISN</th>
                        <th>Nama Lengkap</th>
                        <th>Asal Sekolah</th>
                        <th>Gelombang</th>
                        <th>Status</th>
                        <th class="text-center" style="width:160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0):
                        $no = 1;
                        while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-3"><?= $no++ ?></td>
                        <td><code><?= htmlspecialchars($row['nisn']) ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($row['asal_sekolah']) ?></td>
                        <td>
                            <?php
                            $gel_color = match($row['gelombang']) {
                                'Gelombang 1' => 'success',
                                'Gelombang 2' => 'warning text-dark',
                                'Gelombang 3' => 'purple',
                                default       => 'secondary',
                            };
                            ?>
                            <span class="badge bg-<?= $gel_color === 'purple' ? 'secondary' : $gel_color ?>">
                                <?= htmlspecialchars($row['gelombang']) ?>
                            </span>
                        </td>
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
                        <td class="text-center">
                            <!-- Tombol Detail -->
                            <a href="detail_siswa.php?id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-info text-white me-1" title="Lihat Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <!-- Tombol Edit -->
                            <a href="edit_siswa.php?id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-warning text-white me-1" title="Edit Data">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <!-- Tombol Hapus (pakai JavaScript konfirmasi) -->
                            <button type="button" class="btn btn-sm btn-danger" title="Hapus Data"
                                    onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nama_lengkap'])) ?>')">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-search fs-3 d-block mb-2"></i>
                            <?= !empty($keyword) ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data pendaftar.' ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
