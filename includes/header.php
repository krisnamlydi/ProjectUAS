<?php
// ============================================================
// FILE: includes/header.php
// Fungsi: Template header (navbar + sidebar) untuk semua halaman admin
// ============================================================

// Cek apakah admin sudah login, jika tidak redirect ke halaman login
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — ' : '' ?>SPMB SMK KP Baros</title>
    <!-- Bootstrap 5 CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<!-- ===== NAVBAR ATAS ===== -->
<nav class="navbar navbar-expand-lg navbar-dark spmb-navbar">
    <div class="container-fluid px-4">
        <!-- Logo + Nama Sekolah -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>dashboard.php">
            <img src="<?= BASE_URL ?>assets/img/logo.jpg" alt="Logo SMK KP Baros" class="navbar-logo">
            <div class="lh-sm">
                <div class="fw-bold fs-6 text-white">SPMB SMK KP BAROS</div>
                <div class="text-white-50" style="font-size:0.7rem;">Sistem Penerimaan Murid Baru</div>
            </div>
        </a>

        <!-- Info Admin + Tombol Logout (kanan) -->
        <div class="d-flex align-items-center gap-3 ms-auto">
            <span class="text-white d-none d-md-inline">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($_SESSION['admin_nama']) ?>
            </span>
            <a href="<?= BASE_URL ?>logout.php" class="btn btn-sm btn-outline-light">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<!-- ===== WRAPPER UTAMA (Sidebar + Konten) ===== -->
<div class="d-flex" id="main-wrapper">

    <!-- ===== SIDEBAR KIRI ===== -->
    <nav id="sidebar" class="spmb-sidebar d-flex flex-column p-3">
        <p class="sidebar-heading text-uppercase text-white-50 small mb-2 px-2 mt-1">Menu Utama</p>
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a href="<?= BASE_URL ?>dashboard.php"
                   class="nav-link sidebar-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>pages/daftar_siswa.php"
                   class="nav-link sidebar-link <?= (basename($_SERVER['PHP_SELF']) == 'daftar_siswa.php') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i> Daftar Pendaftar
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>pages/tambah_siswa.php"
                   class="nav-link sidebar-link <?= (basename($_SERVER['PHP_SELF']) == 'tambah_siswa.php') ? 'active' : '' ?>">
                    <i class="bi bi-person-plus-fill"></i> Tambah Pendaftar
                </a>
            </li>
        </ul>
    </nav>
    <!-- ===== AKHIR SIDEBAR ===== -->

    <!-- ===== AREA KONTEN UTAMA ===== -->
    <main class="flex-grow-1 p-4 spmb-content">
