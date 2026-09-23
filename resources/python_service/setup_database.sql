-- ============================================================
-- DATABASE SETUP SCRIPT
-- Loket Antrian System - Database Driven Version
-- Created: January 2026
-- ============================================================

-- 1. CREATE DATABASE
CREATE DATABASE IF NOT EXISTS `antrian_bot`;
USE `antrian_bot`;

-- 2. CREATE TABLE: loket
-- ============================================================
CREATE TABLE IF NOT EXISTS `loket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_loket` varchar(100) NOT NULL COMMENT 'Nama loket',
  `nama_pelayanan` varchar(100) NOT NULL COMMENT 'Jenis pelayanan',
  `status_pelayanan` varchar(50) DEFAULT 'buka' COMMENT 'Status pelayanan (buka/tutup)',
  `status_loket` varchar(50) DEFAULT 'buka' COMMENT 'Status loket (aktif/nonaktif)',
  `waktu_terakhir` time DEFAULT NULL COMMENT 'Waktu terakhir update',
  `tanggal` date DEFAULT NULL COMMENT 'Tanggal terakhir update',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Path ke logo file',
  PRIMARY KEY (`id`),
  KEY `idx_status_loket` (`status_loket`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Daftar loket pelayanan';

-- 3. INSERT DATA: loket
-- ============================================================
INSERT INTO `loket` (`id`, `nama_loket`, `nama_pelayanan`, `status_pelayanan`, `status_loket`) VALUES
(1, 'SAMSAT BANJARBARU', 'Pajak Kendaraan', 'buka', 'buka'),
(2, 'SAMSAT KALSEL', 'Pajak Kendaraan', 'buka', 'buka'),
(3, 'BPJS Kesehatan', 'Kesehatan', 'buka', 'buka'),
(4, 'DUKCAPIL', 'Kependudukan', 'buka', 'buka'),
(5, 'DISNAKER', 'Ketenagakerjaan', 'buka', 'buka');

-- 4. CREATE TABLE: algoritma
-- ============================================================
CREATE TABLE IF NOT EXISTS `algoritma` (
  `id_algoritma` int NOT NULL AUTO_INCREMENT,
  `id_loket` int NOT NULL COMMENT 'Reference ke loket(id)',
  `algoritma` varchar(255) NOT NULL COMMENT 'Keyword untuk deteksi (misal: pajak motor)',
  `tanggal_dan_waktu` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp record dibuat',
  `tipe_layanan` varchar(100) DEFAULT 'UMUM' COMMENT 'Kategori layanan (UMUM, DARURAT, LANSIA)',
  `lokasi` varchar(100) DEFAULT NULL COMMENT 'YA jika loket memerlukan lokasi info, NULL jika tidak',
  PRIMARY KEY (`id_algoritma`),
  KEY `idx_id_loket` (`id_loket`),
  KEY `idx_algoritma` (`algoritma`),
  CONSTRAINT `fk_algoritma_loket` FOREIGN KEY (`id_loket`) REFERENCES `loket` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Algoritma deteksi keyword untuk routing ke loket';

-- 5. INSERT DATA: algoritma
-- ============================================================

-- SAMSAT BANJARBARU (dengan lokasi = 'YA')
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`, `lokasi`) VALUES
(1, 'pajak motor banjarbaru', 'UMUM', 'YA'),
(1, 'pajak mobil banjarbaru', 'UMUM', 'YA'),
(1, 'stnk banjarbaru', 'UMUM', 'YA'),
(1, 'pajak kendaraan banjarbaru', 'UMUM', 'YA'),
(1, 'kendaraan bermotor banjarbaru', 'UMUM', 'YA'),
(1, 'pajak jatuh tempo banjarbaru', 'UMUM', 'YA');

-- SAMSAT KALSEL (dengan lokasi = 'YA')
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`, `lokasi`) VALUES
(2, 'pajak motor banjarmasin', 'UMUM', 'YA'),
(2, 'pajak kendaraan kalsel', 'UMUM', 'YA'),
(2, 'pajak motor kandangan', 'UMUM', 'YA'),
(2, 'pajak kendaraan tapin', 'UMUM', 'YA'),
(2, 'pajak motor tabalong', 'UMUM', 'YA'),
(2, 'pajak motor balangan', 'UMUM', 'YA'),
(2, 'pajak motor tanah laut', 'UMUM', 'YA'),
(2, 'pajak motor barito kuala', 'UMUM', 'YA'),
(2, 'pajak motor tanah bumbu', 'UMUM', 'YA'),
(2, 'stnk kalsel', 'UMUM', 'YA');

-- BPJS KESEHATAN (tanpa lokasi = NULL)
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`, `lokasi`) VALUES
(3, 'bpjs kesehatan', 'UMUM', NULL),
(3, 'bpjs jkn', 'UMUM', NULL),
(3, 'bpjs kis', 'UMUM', NULL),
(3, 'kartu sehat', 'UMUM', NULL),
(3, 'jaminan kesehatan nasional', 'UMUM', NULL),
(3, 'kartu indonesia sehat', 'UMUM', NULL);

-- DUKCAPIL (tanpa lokasi = NULL)
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`, `lokasi`) VALUES
(4, 'ktp elektronik', 'UMUM', NULL),
(4, 'kartu keluarga', 'UMUM', NULL),
(4, 'akta lahir', 'UMUM', NULL),
(4, 'akta nikah', 'UMUM', NULL),
(4, 'surat pindah', 'UMUM', NULL),
(4, 'cerai', 'UMUM', NULL),
(4, 'e-ktp', 'UMUM', NULL),
(4, 'perubahan data', 'UMUM', NULL);

-- DISNAKER (tanpa lokasi = NULL)
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`, `lokasi`) VALUES
(5, 'cari kerja', 'UMUM', NULL),
(5, 'lowongan kerja', 'UMUM', NULL),
(5, 'kartu kuning', 'UMUM', NULL),
(5, 'sertifikat kerja', 'UMUM', NULL),
(5, 'surat keterangan kerja', 'UMUM', NULL),
(5, 'pelatihan kerja', 'UMUM', NULL);

-- 6. VERIFICATION QUERIES
-- ============================================================

-- Lihat semua data loket
-- SELECT * FROM loket;

-- Lihat semua data algoritma dengan nama loket
-- SELECT a.*, l.nama_loket, l.nama_pelayanan 
-- FROM algoritma a 
-- JOIN loket l ON a.id_loket = l.id
-- ORDER BY l.id, a.id_algoritma;

-- Lihat loket yang memerlukan lokasi (SAMSAT)
-- SELECT DISTINCT l.id, l.nama_loket, COUNT(a.id_algoritma) as keyword_count
-- FROM loket l
-- LEFT JOIN algoritma a ON l.id = a.id_loket AND a.lokasi = 'YA'
-- GROUP BY l.id, l.nama_loket;

-- Lihat loket yang tidak memerlukan lokasi
-- SELECT DISTINCT l.id, l.nama_loket, COUNT(a.id_algoritma) as keyword_count
-- FROM loket l
-- LEFT JOIN algoritma a ON l.id = a.id_loket AND a.lokasi IS NULL
-- GROUP BY l.id, l.nama_loket;

-- Count total keywords
-- SELECT COUNT(*) as total_keywords FROM algoritma;

-- ============================================================
-- NOTES:
-- ============================================================
-- 1. Kolom 'lokasi' sangat penting:
--    - 'YA' = loket memerlukan informasi lokasi (SAMSAT)
--    - NULL = loket tidak memerlukan lokasi (BPJS, DUKCAPIL, DISNAKER)
--
-- 2. Tabel algoritma bisa memiliki banyak keywords per loket
--    Contoh: loket BPJS memiliki 6 keyword berbeda
--
-- 3. Untuk menambah keyword baru:
--    INSERT INTO algoritma (id_loket, algoritma, tipe_layanan, lokasi)
--    VALUES (1, 'pajak motor baru', 'UMUM', 'YA');
--
-- 4. Untuk mengubah kolom 'lokasi' pada loket:
--    UPDATE algoritma SET lokasi = 'YA' WHERE id_loket = 5;
--
-- 5. Index sudah dibuat untuk performa:
--    - idx_status_loket di loket
--    - idx_id_loket di algoritma
--    - idx_algoritma di algoritma
--
-- 6. Foreign key constraint memastikan data integrity
--    Tidak bisa delete loket jika ada algoritma yang reference-nya
--
-- ============================================================
