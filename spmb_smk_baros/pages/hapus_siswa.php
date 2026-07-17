<?php
// ============================================================
// FILE: pages/hapus_siswa.php
// Fungsi: Menghapus satu data calon siswa berdasarkan ID
// Halaman ini tidak memiliki tampilan (hanya proses & redirect)
// ============================================================

session_start();

require_once '../includes/koneksi.php';

// ===== AMBIL ID DARI URL =====
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    // ID tidak valid, kembali ke daftar
    header("Location: daftar_siswa.php");
    exit();
}

// ===== AMBIL NAMA SISWA DULU (untuk pesan notifikasi) =====
$cek = $koneksi->prepare("SELECT nama_lengkap FROM calon_siswa WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$data = $cek->get_result()->fetch_assoc();
$cek->close();

if (!$data) {
    // Data tidak ditemukan
    $_SESSION['notif_msg']  = 'Data yang ingin dihapus tidak ditemukan.';
    $_SESSION['notif_type'] = 'warning';
    header("Location: daftar_siswa.php");
    exit();
}

// ===== EKSEKUSI HAPUS DATA =====
$stmt = $koneksi->prepare("DELETE FROM calon_siswa WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['notif_msg']  = "Data pendaftar <strong>" . htmlspecialchars($data['nama_lengkap']) . "</strong> berhasil dihapus.";
    $_SESSION['notif_type'] = 'success';
} else {
    $_SESSION['notif_msg']  = 'Gagal menghapus data. Silakan coba lagi.';
    $_SESSION['notif_type'] = 'danger';
}

$stmt->close();

// Redirect kembali ke halaman daftar
header("Location: daftar_siswa.php");
exit();
?>
