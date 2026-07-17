-- ============================================================
-- DATABASE: SPMB SMK KP BAROS
-- Sistem Penerimaan Murid Baru
-- ============================================================

CREATE DATABASE IF NOT EXISTS spmb_smk_baros
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE spmb_smk_baros;

-- ------------------------------------------------------------
-- TABEL 1: users
-- Menyimpan data akun admin yang dapat login ke sistem
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT(11) NOT NULL AUTO_INCREMENT,
    nama        VARCHAR(100) NOT NULL,
    username    VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,   -- disimpan dalam bentuk hash (password_hash)
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABEL 2: calon_siswa
-- Menyimpan seluruh data pendaftar SPMB
-- Tidak ada kolom jurusan (Kurikulum Merdeka)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS calon_siswa (
    id              INT(11) NOT NULL AUTO_INCREMENT,
    nisn            VARCHAR(20) NOT NULL UNIQUE,
    nama_lengkap    VARCHAR(150) NOT NULL,
    tempat_lahir    VARCHAR(100) NOT NULL,
    tanggal_lahir   DATE NOT NULL,
    jenis_kelamin   ENUM('Laki-laki', 'Perempuan') NOT NULL,
    asal_sekolah    VARCHAR(150) NOT NULL,
    alamat          TEXT NOT NULL,
    no_telepon      VARCHAR(20),
    nama_orang_tua  VARCHAR(150) NOT NULL,
    gelombang       ENUM('Gelombang 1', 'Gelombang 2', 'Gelombang 3') NOT NULL,
    status          ENUM('Pending', 'Diterima', 'Ditolak') NOT NULL DEFAULT 'Pending',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DML: DATA DUMMY
-- ============================================================

-- Password untuk semua admin adalah: admin123
-- Hash dibuat dengan: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (nama, username, password) VALUES
('Administrator Utama', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Operator SPMB',       'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Data dummy calon siswa (10 pendaftar)
INSERT INTO calon_siswa (nisn, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, asal_sekolah, alamat, no_telepon, nama_orang_tua, gelombang, status) VALUES
('0081234501', 'Ahmad Fauzi Ramdan',    'Sukabumi', '2008-03-15', 'Laki-laki',   'SMP N 1 Baros',      'Jl. Merdeka No. 12, Baros',       '081234567890', 'Bapak Ramdan',    'Gelombang 1', 'Diterima'),
('0081234502', 'Siti Nurhaliza',        'Cianjur',  '2008-07-22', 'Perempuan',   'SMP N 2 Cibadak',    'Jl. Pahlawan No. 5, Sukabumi',    '082345678901', 'Ibu Halimah',     'Gelombang 1', 'Diterima'),
('0081234503', 'Deden Supriatna',       'Sukabumi', '2008-11-01', 'Laki-laki',   'MTs Al-Hidayah',     'Kp. Baros Hilir RT 02/03',        '083456789012', 'Bapak Supriatna', 'Gelombang 1', 'Pending'),
('0081234504', 'Rini Anggraeni',        'Bogor',    '2009-01-18', 'Perempuan',   'SMP PGRI Baros',     'Jl. Veteran No. 8, Baros',        '084567890123', 'Ibu Anggraeni',   'Gelombang 1', 'Diterima'),
('0081234505', 'Muhamad Rizky Pratama', 'Sukabumi', '2008-05-30', 'Laki-laki',   'SMP N 3 Sukabumi',   'Perum Griya Indah Blok B-4',      '085678901234', 'Bapak Pratama',   'Gelombang 2', 'Pending'),
('0081234506', 'Dewi Rahayu Putri',     'Sukabumi', '2009-02-14', 'Perempuan',   'SMP Islam Terpadu',  'Jl. Siliwangi No. 21, Sukabumi',  '086789012345', 'Ibu Rahayu',      'Gelombang 2', 'Diterima'),
('0081234507', 'Andi Kurniawan',        'Bandung',  '2008-09-09', 'Laki-laki',   'SMP N 1 Sukabumi',   'Jl. Ahmad Yani No. 33, Sukabumi', '087890123456', 'Bapak Kurniawan', 'Gelombang 2', 'Pending'),
('0081234508', 'Fitri Handayani',       'Sukabumi', '2008-12-25', 'Perempuan',   'SMP Muhammadiyah 1', 'Kp. Ciomas RT 05/02, Baros',      '088901234567', 'Ibu Handayani',   'Gelombang 2', 'Ditolak'),
('0081234509', 'Bayu Setiawan',         'Ciawi',    '2009-04-07', 'Laki-laki',   'MTs Nurul Iman',     'Jl. Raya Ciawi No. 10, Bogor',    '089012345678', 'Bapak Setiawan',  'Gelombang 3', 'Pending'),
('0081234510', 'Indah Permatasari',     'Sukabumi', '2009-06-20', 'Perempuan',   'SMP N 4 Baros',      'Jl. Bunga Rampai No. 7, Baros',   '081123456789', 'Ibu Permata',     'Gelombang 3', 'Pending');
