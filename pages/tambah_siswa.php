<?php
// ============================================================
// FILE: pages/tambah_siswa.php
// Fungsi: Form untuk menambah data calon siswa baru
// ============================================================

session_start();
define('BASE_URL', '../');

require_once '../includes/koneksi.php';

$page_title = 'Tambah Pendaftar';
$errors = [];  // Array untuk menampung error validasi dari PHP

// ===== PROSES SIMPAN DATA (saat form di-submit) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Ambil & bersihkan semua input ---
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
    $status         = trim($_POST['status'] ?? 'Pending');

    // --- Validasi Server-Side (PHP) ---
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

    // --- Cek NISN sudah terdaftar atau belum ---
    if (empty($errors)) {
        $cek = $koneksi->prepare("SELECT id FROM calon_siswa WHERE nisn = ?");
        $cek->bind_param("s", $nisn);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $errors[] = "NISN <strong>{$nisn}</strong> sudah terdaftar di sistem!";
        }
        $cek->close();
    }

    // --- Jika tidak ada error: simpan ke database ---
    if (empty($errors)) {
        $stmt = $koneksi->prepare("
            INSERT INTO calon_siswa
                (nisn, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, asal_sekolah, alamat, no_telepon, nama_orang_tua, gelombang, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sssssssssss",
            $nisn, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $jenis_kelamin,
            $asal_sekolah, $alamat, $no_telepon, $nama_orang_tua, $gelombang, $status
        );

        if ($stmt->execute()) {
            // Berhasil: set pesan notifikasi & redirect ke daftar
            $_SESSION['notif_msg']  = "Data pendaftar <strong>{$nama_lengkap}</strong> berhasil ditambahkan!";
            $_SESSION['notif_type'] = 'success';
            header("Location: daftar_siswa.php");
            exit();
        } else {
            $errors[] = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        }
        $stmt->close();
    }
}

require_once '../includes/header.php';
?>

<!-- ===== JUDUL ===== -->
<div class="mb-4">
    <h1 class="page-title">
        <span class="page-title-bar"></span>
        Tambah Pendaftar Baru
    </h1>
    <a href="daftar_siswa.php" class="btn btn-sm btn-outline-secondary mt-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<!-- ===== TAMPILKAN ERROR VALIDASI JIKA ADA ===== -->
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

<!-- ===== KARTU FORM ===== -->
<div class="card form-card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Formulir Pendaftaran Calon Siswa</h6>
    </div>
    <div class="card-body p-4">
        <!--
            onsubmit="return validasiFormSiswa()"
            Memanggil fungsi validasi JS sebelum form dikirim (client-side)
        -->
        <form method="POST" action="" onsubmit="return validasiFormSiswa()">

            <div class="row g-3">
                <!-- ====== KOLOM KIRI ====== -->

                <!-- NISN -->
                <div class="col-md-6">
                    <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nisn" name="nisn"
                           placeholder="Contoh: 0081234501" maxlength="10"
                           value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>"
                           pattern="\d{10}" title="NISN harus 10 digit angka" required>
                    <div class="form-text">10 digit angka, tanpa spasi atau tanda baca.</div>
                </div>

                <!-- Nama Lengkap -->
                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                           placeholder="Nama lengkap sesuai ijazah"
                           value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
                </div>

                <!-- Tempat Lahir -->
                <div class="col-md-6">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                           placeholder="Contoh: Sukabumi"
                           value="<?= htmlspecialchars($_POST['tempat_lahir'] ?? '') ?>" required>
                </div>

                <!-- Tanggal Lahir -->
                <div class="col-md-6">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                           value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? '') ?>" required>
                </div>

                <!-- Jenis Kelamin -->
                <div class="col-md-6">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki"  <?= (($_POST['jenis_kelamin'] ?? '') === 'Laki-laki')  ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan"  <?= (($_POST['jenis_kelamin'] ?? '') === 'Perempuan')  ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <!-- Asal Sekolah -->
                <div class="col-md-6">
                    <label for="asal_sekolah" class="form-label">Asal Sekolah <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah"
                           placeholder="Contoh: SMP N 1 Baros"
                           value="<?= htmlspecialchars($_POST['asal_sekolah'] ?? '') ?>" required>
                </div>

                <!-- Alamat (full width) -->
                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2"
                              placeholder="Tulis alamat lengkap termasuk RT/RW, Desa/Kelurahan, Kecamatan"
                              required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>

                <!-- No. Telepon -->
                <div class="col-md-6">
                    <label for="no_telepon" class="form-label">No. Telepon / HP</label>
                    <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                           placeholder="Contoh: 081234567890"
                           value="<?= htmlspecialchars($_POST['no_telepon'] ?? '') ?>">
                    <div class="form-text">Opsional. Isi dengan nomor HP aktif.</div>
                </div>

                <!-- Nama Orang Tua -->
                <div class="col-md-6">
                    <label for="nama_orang_tua" class="form-label">Nama Orang Tua/Wali <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_orang_tua" name="nama_orang_tua"
                           placeholder="Nama Ayah / Ibu / Wali"
                           value="<?= htmlspecialchars($_POST['nama_orang_tua'] ?? '') ?>" required>
                </div>

                <!-- Gelombang -->
                <div class="col-md-6">
                    <label for="gelombang" class="form-label">Gelombang Pendaftaran <span class="text-danger">*</span></label>
                    <select class="form-select" id="gelombang" name="gelombang" required>
                        <option value="">-- Pilih Gelombang --</option>
                        <option value="Gelombang 1" <?= (($_POST['gelombang'] ?? '') === 'Gelombang 1') ? 'selected' : '' ?>>Gelombang 1</option>
                        <option value="Gelombang 2" <?= (($_POST['gelombang'] ?? '') === 'Gelombang 2') ? 'selected' : '' ?>>Gelombang 2</option>
                        <option value="Gelombang 3" <?= (($_POST['gelombang'] ?? '') === 'Gelombang 3') ? 'selected' : '' ?>>Gelombang 3</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label for="status" class="form-label">Status Penerimaan</label>
                    <select class="form-select" id="status" name="status">
                        <option value="Pending"  <?= (($_POST['status'] ?? 'Pending') === 'Pending')  ? 'selected' : '' ?>>Pending</option>
                        <option value="Diterima" <?= (($_POST['status'] ?? '') === 'Diterima') ? 'selected' : '' ?>>Diterima</option>
                        <option value="Ditolak"  <?= (($_POST['status'] ?? '') === 'Ditolak')  ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>

                <!-- Tombol Submit -->
                <div class="col-12 mt-2">
                    <hr>
                    <button type="submit" class="btn btn-spmb-primary">
                        <i class="bi bi-save-fill me-1"></i> Simpan Data
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
