<?php
// ============================================================
// FILE: pages/edit_siswa.php
// Fungsi: Form untuk mengedit/mengubah data calon siswa
// ============================================================

session_start();
define('BASE_URL', '../');

require_once '../includes/koneksi.php';

$page_title = 'Edit Data Pendaftar';
$errors = [];

// ===== AMBIL ID DARI URL =====
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: daftar_siswa.php");
    exit();
}

// ===== AMBIL DATA LAMA DARI DATABASE (untuk mengisi form) =====
$stmt = $koneksi->prepare("SELECT * FROM calon_siswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: daftar_siswa.php");
    exit();
}

// Simpan data lama (akan dipakai mengisi form default)
$siswa = $result->fetch_assoc();
$stmt->close();

// ===== PROSES UPDATE DATA (saat form di-submit) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil input baru dari form
    $nisn           = trim($_POST['nisn'] ?? '');
    $nama_lengkap   = trim($_POST['nama_lengkap'] ?? '');
    $tempat_lahir   = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir  = trim($_POST['tanggal_lahir'] ?? '');
    $jenis_kelamin  = trim($_POST['jenis_kelamin'] ?? '');
    $asal_sekolah   = trim($_POST['asal_sekolah'] ?? '');
    $alamat         = trim($_POST['alamat'] ?? '');
    $no_telepon     = trim($_POST['no_telepon'] ?? '');
    $nama_orang_tua = trim($_POST['nama_orang_tua'] ?? '');
    $gelombang      = trim($_POST['gelombang'] ?? '');
    $status         = trim($_POST['status'] ?? '');

    // --- Validasi ---
    if (empty($nisn) || !preg_match('/^\d{10}$/', $nisn)) {
        $errors[] = 'NISN harus berupa 10 digit angka.';
    }
    if (empty($nama_lengkap))    $errors[] = 'Nama Lengkap tidak boleh kosong.';
    if (empty($tempat_lahir))    $errors[] = 'Tempat Lahir tidak boleh kosong.';
    if (empty($tanggal_lahir))   $errors[] = 'Tanggal Lahir tidak boleh kosong.';
    if (empty($jenis_kelamin))   $errors[] = 'Jenis Kelamin harus dipilih.';
    if (empty($asal_sekolah))    $errors[] = 'Asal Sekolah tidak boleh kosong.';
    if (empty($alamat))          $errors[] = 'Alamat tidak boleh kosong.';
    if (empty($nama_orang_tua))  $errors[] = 'Nama Orang Tua tidak boleh kosong.';
    if (empty($gelombang))       $errors[] = 'Gelombang harus dipilih.';

    // Cek NISN unik — kecuali NISN milik siswa yang sedang diedit
    if (empty($errors)) {
        $cek = $koneksi->prepare("SELECT id FROM calon_siswa WHERE nisn = ? AND id != ?");
        $cek->bind_param("si", $nisn, $id);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $errors[] = "NISN <strong>{$nisn}</strong> sudah digunakan oleh pendaftar lain!";
        }
        $cek->close();
    }

    // --- Jika tidak ada error: update data di database ---
    if (empty($errors)) {
        $stmt = $koneksi->prepare("
            UPDATE calon_siswa SET
                nisn=?, nama_lengkap=?, tempat_lahir=?, tanggal_lahir=?,
                jenis_kelamin=?, asal_sekolah=?, alamat=?, no_telepon=?,
                nama_orang_tua=?, gelombang=?, status=?
            WHERE id=?
        ");
        $stmt->bind_param(
            "sssssssssssi",
            $nisn, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $jenis_kelamin,
            $asal_sekolah, $alamat, $no_telepon, $nama_orang_tua, $gelombang, $status, $id
        );

        if ($stmt->execute()) {
            $_SESSION['notif_msg']  = "Data <strong>{$nama_lengkap}</strong> berhasil diperbarui!";
            $_SESSION['notif_type'] = 'success';
            header("Location: daftar_siswa.php");
            exit();
        } else {
            $errors[] = 'Gagal memperbarui data. Silakan coba lagi.';
        }
        $stmt->close();

        // Perbarui variabel $siswa agar form menampilkan input terbaru (bukan data lama)
        $siswa = array_merge($siswa, compact(
            'nisn','nama_lengkap','tempat_lahir','tanggal_lahir',
            'jenis_kelamin','asal_sekolah','alamat','no_telepon',
            'nama_orang_tua','gelombang','status'
        ));
    }
}

require_once '../includes/header.php';
?>

<!-- ===== JUDUL ===== -->
<div class="mb-4">
    <h1 class="page-title">
        <span class="page-title-bar"></span>
        Edit Data Pendaftar
    </h1>
    <a href="daftar_siswa.php" class="btn btn-sm btn-outline-secondary mt-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<!-- ===== TAMPILKAN ERROR ===== -->
<?php if (!empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Terdapat Kesalahan:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach ($errors as $err): ?>
            <li><?= $err ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ===== KARTU FORM EDIT ===== -->
<div class="card form-card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i>
            Edit: <?= htmlspecialchars($siswa['nama_lengkap']) ?>
        </h6>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="edit_siswa.php?id=<?= $id ?>" onsubmit="return validasiFormSiswa()">

            <div class="row g-3">
                <!-- NISN -->
                <div class="col-md-6">
                    <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nisn" name="nisn"
                           maxlength="10" pattern="\d{10}" title="NISN harus 10 digit angka"
                           value="<?= htmlspecialchars($siswa['nisn']) ?>" required>
                </div>

                <!-- Nama Lengkap -->
                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                           value="<?= htmlspecialchars($siswa['nama_lengkap']) ?>" required>
                </div>

                <!-- Tempat Lahir -->
                <div class="col-md-6">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                           value="<?= htmlspecialchars($siswa['tempat_lahir']) ?>" required>
                </div>

                <!-- Tanggal Lahir -->
                <div class="col-md-6">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                           value="<?= htmlspecialchars($siswa['tanggal_lahir']) ?>" required>
                </div>

                <!-- Jenis Kelamin -->
                <div class="col-md-6">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki"  <?= ($siswa['jenis_kelamin'] === 'Laki-laki')  ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan"  <?= ($siswa['jenis_kelamin'] === 'Perempuan')  ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <!-- Asal Sekolah -->
                <div class="col-md-6">
                    <label for="asal_sekolah" class="form-label">Asal Sekolah <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah"
                           value="<?= htmlspecialchars($siswa['asal_sekolah']) ?>" required>
                </div>

                <!-- Alamat -->
                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2" required><?= htmlspecialchars($siswa['alamat']) ?></textarea>
                </div>

                <!-- No Telepon -->
                <div class="col-md-6">
                    <label for="no_telepon" class="form-label">No. Telepon</label>
                    <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                           value="<?= htmlspecialchars($siswa['no_telepon']) ?>">
                </div>

                <!-- Nama Orang Tua -->
                <div class="col-md-6">
                    <label for="nama_orang_tua" class="form-label">Nama Orang Tua/Wali <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_orang_tua" name="nama_orang_tua"
                           value="<?= htmlspecialchars($siswa['nama_orang_tua']) ?>" required>
                </div>

                <!-- Gelombang -->
                <div class="col-md-6">
                    <label for="gelombang" class="form-label">Gelombang <span class="text-danger">*</span></label>
                    <select class="form-select" id="gelombang" name="gelombang" required>
                        <option value="">-- Pilih --</option>
                        <option value="Gelombang 1" <?= ($siswa['gelombang'] === 'Gelombang 1') ? 'selected' : '' ?>>Gelombang 1</option>
                        <option value="Gelombang 2" <?= ($siswa['gelombang'] === 'Gelombang 2') ? 'selected' : '' ?>>Gelombang 2</option>
                        <option value="Gelombang 3" <?= ($siswa['gelombang'] === 'Gelombang 3') ? 'selected' : '' ?>>Gelombang 3</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label for="status" class="form-label">Status Penerimaan</label>
                    <select class="form-select" id="status" name="status">
                        <option value="Pending"  <?= ($siswa['status'] === 'Pending')  ? 'selected' : '' ?>>Pending</option>
                        <option value="Diterima" <?= ($siswa['status'] === 'Diterima') ? 'selected' : '' ?>>Diterima</option>
                        <option value="Ditolak"  <?= ($siswa['status'] === 'Ditolak')  ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>

                <!-- Tombol -->
                <div class="col-12 mt-2">
                    <hr>
                    <button type="submit" class="btn btn-warning text-white fw-semibold">
                        <i class="bi bi-save-fill me-1"></i> Simpan Perubahan
                    </button>
                    <a href="daftar_siswa.php" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
