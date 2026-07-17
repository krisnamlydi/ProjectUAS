<?php
// ============================================================
// FILE: logout.php
// Fungsi: Menghancurkan sesi dan mengarahkan ke halaman login
// ============================================================

session_start();    // Mulai sesi (agar bisa dihapus)
session_destroy();  // Hapus semua data sesi

// Redirect ke halaman login
header("Location: index.php");
exit();
?>
