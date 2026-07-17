// ============================================================
// FILE: assets/js/script.js
// Fungsi: JavaScript kustom untuk validasi dan interaksi UI
// ============================================================

// ===== KONFIRMASI HAPUS DATA =====
// Fungsi ini dipanggil dari tombol Hapus di tabel
function konfirmasiHapus(id, nama) {
    const pesan = `Anda yakin ingin menghapus data pendaftar:\n\n"${nama}"\n\nData yang dihapus tidak dapat dikembalikan!`;
    if (confirm(pesan)) {
        // Jika dikonfirmasi, arahkan ke URL proses hapus
        window.location.href = `../pages/hapus_siswa.php?id=${id}`;
    }
}

// ===== VALIDASI FORM TAMBAH/EDIT SISWA =====
// Dijalankan saat form akan di-submit
function validasiFormSiswa() {
    const nisn = document.getElementById('nisn');
    const nama = document.getElementById('nama_lengkap');
    const noTelp = document.getElementById('no_telepon');

    // Validasi NISN: harus angka dan 10 digit
    if (nisn && !/^\d{10}$/.test(nisn.value.trim())) {
        alert('NISN harus berupa 10 digit angka!');
        nisn.focus();
        return false;
    }

    // Validasi Nama: tidak boleh kosong
    if (nama && nama.value.trim() === '') {
        alert('Nama Lengkap tidak boleh kosong!');
        nama.focus();
        return false;
    }

    // Validasi Nomor Telepon: hanya angka jika diisi
    if (noTelp && noTelp.value.trim() !== '' && !/^\d{9,15}$/.test(noTelp.value.trim())) {
        alert('Nomor telepon tidak valid! Isi dengan angka (9-15 digit).');
        noTelp.focus();
        return false;
    }

    return true; // Form valid, izinkan submit
}

// ===== AUTO DISMISS ALERT =====
// Alert notifikasi akan menghilang otomatis setelah 4 detik
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert-auto-dismiss');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            // Gunakan Bootstrap API untuk menutup alert dengan animasi
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 4000);
    });
});
