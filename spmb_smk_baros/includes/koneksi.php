<?php
// ============================================================
// FILE: includes/koneksi.php
// Fungsi: Membuat koneksi ke database MySQL menggunakan MySQLi
// ============================================================

// --- Konfigurasi Database ---
// Ubah nilai-nilai di bawah ini sesuai dengan setting server lokal Anda (XAMPP/Laragon)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Username database (default XAMPP: root)
define('DB_PASS', '');           // Password database (default XAMPP: kosong)
define('DB_NAME', 'spmb_smk_baros');

// --- Membuat Koneksi ---
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- Cek apakah koneksi berhasil ---
if ($koneksi->connect_error) {
    // Jika gagal, hentikan program dan tampilkan pesan error
    die("<div style='font-family:sans-serif;color:red;padding:20px;'>
            <strong>Koneksi Database Gagal!</strong><br>
            Error: " . $koneksi->connect_error . "<br><br>
            <em>Pastikan XAMPP berjalan dan database 'spmb_smk_baros' sudah dibuat.</em>
         </div>");
}

// Set charset agar karakter Indonesia (huruf khusus) tampil dengan benar
$koneksi->set_charset("utf8mb4");
?>
