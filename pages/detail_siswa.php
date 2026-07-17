<?php
// ============================================================
// FILE: pages/detail_siswa.php
// Fungsi: Menampilkan detail lengkap satu data calon siswa
// ============================================================

session_start();
define('BASE_URL', '../');

require_once '../includes/koneksi.php';

$page_title = 'Detail Pendaftar';

// ===== AMBIL ID DARI URL =====
$id = intval($_GET['id'] ?? 0); // intval() memastikan ini adalah integer (aman)

if ($id <= 0) {
    // Jika ID tidak valid, redirect kembali
    header("Location: daftar_siswa.php");
    exit();
}

// ===== QUERY DATA SISWA BERDASARKAN ID =====
$stmt = $koneksi->prepare("SELECT * FROM calon_siswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika data tidak ditemukan, redirect
    header("Location: daftar_siswa.php");
    exit();
}

$siswa = $result->fetch_assoc();
$stmt->close();

// Format tanggal lahir menjadi format Indonesia (misal: 15 Maret 2008)
$bulan_id = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
    7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
];
$tgl = explode('-', $siswa['tanggal_lahir']);
$tanggal_format = $tgl[2] . ' ' . $bulan_id[(int)$tgl[1]] . ' ' . $tgl[0];

require_once '../includes/header.php';
?>

<!-- ===== BREADCRUMB + AKSI ===== -->
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h1 class="page-title">
            <span class="page-title-bar"></span>
            Detail Pendaftar
        </h1>
        <a href="daftar_siswa.php" class="btn btn-sm btn-outline-secondary mt-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="d-flex gap-2">
        <a href="edit_siswa.php?id=<?= $siswa['id'] ?>" class="btn btn-warning text-white">
            <i class="bi bi-pencil-fill me-1"></i> Edit Data
        </a>
        <button type="button" class="btn btn-danger"
                onclick="konfirmasiHapus(<?= $siswa['id'] ?>, '<?= htmlspecialchars(addslashes($siswa['nama_lengkap'])) ?>')">
            <i class="bi bi-trash-fill me-1"></i> Hapus
        </button>
    </div>
</div>

<!-- ===== KARTU DETAIL ===== -->
<div class="card form-card">
    <!-- Header kartu -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-person-vcard-fill me-2"></i>
            Informasi Calon Siswa
        </h6>
        <!-- Badge Status -->
        <?php
        $status_class = match($siswa['status']) {
            'Diterima' => 'badge-diterima',
            'Ditolak'  => 'badge-ditolak',
            default    => 'badge-pending',
        };
        ?>
        <span class="badge fs-6 <?= $status_class ?>">
            <?= htmlspecialchars($siswa['status']) ?>
        </span>
    </div>

    <div class="card-body p-4">
        <div class="row g-4">

            <!-- ====== KOLOM KIRI: Data Pribadi ====== -->
            <div class="col-md-6">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-person-fill me-1"></i> Data Pribadi
                </h6>

                <div class="mb-3">
                    <p class="detail-label mb-1">NISN</p>
                    <p class="detail-value"><code class="fs-6"><?= htmlspecialchars($siswa['nisn']) ?></code></p>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">Nama Lengkap</p>
                    <p class="detail-value fw-semibold fs-5"><?= htmlspecialchars($siswa['nama_lengkap']) ?></p>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">Tempat, Tanggal Lahir</p>
                    <p class="detail-value"><?= htmlspecialchars($siswa['tempat_lahir']) ?>, <?= $tanggal_format ?></p>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">Jenis Kelamin</p>
                    <p class="detail-value"><?= htmlspecialchars($siswa['jenis_kelamin']) ?></p>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">Nama Orang Tua / Wali</p>
                    <p class="detail-value"><?= htmlspecialchars($siswa['nama_orang_tua']) ?></p>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">No. Telepon</p>
                    <p class="detail-value">
                        <?= !empty($siswa['no_telepon']) ? htmlspecialchars($siswa['no_telepon']) : '<span class="text-muted">-</span>' ?>
                    </p>
                </div>
            </div>

            <!-- ====== KOLOM KANAN: Data Pendaftaran ====== -->
            <div class="col-md-6">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-clipboard2-data-fill me-1"></i> Data Pendaftaran
                </h6>

                <div class="mb-3">
                    <p class="detail-label mb-1">Asal Sekolah</p>
                    <p class="detail-value"><?= htmlspecialchars($siswa['asal_sekolah']) ?></p>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">Alamat Lengkap</p>
                    <p class="detail-value"><?= nl2br(htmlspecialchars($siswa['alamat'])) ?></p>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">Gelombang Pendaftaran</p>
                    <span class="badge bg-info text-dark fs-6"><?= htmlspecialchars($siswa['gelombang']) ?></span>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">Status Penerimaan</p>
                    <span class="badge fs-6 <?= $status_class ?>"><?= htmlspecialchars($siswa['status']) ?></span>
                </div>
                <div class="mb-3">
                    <p class="detail-label mb-1">Tanggal Mendaftar</p>
                    <p class="detail-value"><?= date('d M Y, H:i', strtotime($siswa['created_at'])) ?> WIB</p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
