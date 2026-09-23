-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 09 Jan 2026 pada 08.18
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `antrian_bot`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `algoritma`
--

CREATE TABLE `algoritma` (
  `id_algoritma` int(11) NOT NULL,
  `id_loket` int(11) NOT NULL,
  `algoritma` varchar(255) NOT NULL,
  `tanggal_dan_waktu` datetime NOT NULL,
  `tipe_layanan` enum('layanan','lokasi','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `algoritma`
--

INSERT INTO `algoritma` (`id_algoritma`, `id_loket`, `algoritma`, `tanggal_dan_waktu`, `tipe_layanan`) VALUES
(1, 1, 'pajak motor', '2025-12-25 08:37:53', 'layanan'),
(2, 1, 'kendaraan', '2025-12-25 15:56:56', 'layanan'),
(3, 1, 'pajak tahunan', '2025-12-25 15:57:27', 'layanan'),
(4, 1, 'stnk', '2025-12-25 15:57:50', 'layanan'),
(5, 2, 'pajak motor', '2025-12-25 15:58:19', 'layanan'),
(6, 2, 'kendaraan', '2025-12-25 15:58:51', 'layanan'),
(7, 2, 'pajak tahunan', '2025-12-25 15:59:33', 'layanan'),
(8, 2, 'stnk', '2025-12-25 15:59:53', 'layanan'),
(9, 1, 'banjarbaru', '2025-12-25 16:21:30', 'lokasi'),
(10, 2, 'banjarmasin', '2025-12-25 16:21:59', 'lokasi'),
(11, 2, 'batu licin', '2025-12-25 16:22:39', 'lokasi'),
(12, 2, 'martapura', '2025-12-25 16:26:14', 'lokasi'),
(13, 3, 'bpjs kesehatan', '2025-12-25 16:27:22', 'layanan'),
(14, 3, 'faskes', '2025-12-25 16:27:57', 'layanan'),
(15, 3, 'paskes', '2025-12-25 16:28:37', 'layanan'),
(16, 3, 'jkn', '2025-12-25 16:29:08', 'layanan'),
(17, 3, 'kis', '2025-12-25 16:29:52', 'layanan'),
(18, 4, 'ktp', '2025-12-25 16:30:07', 'layanan'),
(19, 4, 'kartu keluarga', '2025-12-25 16:30:44', 'layanan'),
(20, 4, 'kartu kk', '2025-12-25 16:31:10', 'layanan'),
(21, 4, 'kk', '2025-12-25 16:31:28', 'layanan'),
(22, 4, 'akta', '2025-12-25 16:31:41', 'layanan'),
(23, 4, 'surat kematian', '2025-12-25 16:32:04', 'layanan'),
(24, 5, 'kartu kuning', '2025-12-25 16:32:42', 'layanan'),
(25, 5, 'pencari kerja', '2025-12-25 16:32:57', 'layanan'),
(26, 3, 'kartu kesehatan', '2026-01-09 04:02:54', 'layanan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `antrian`
--

CREATE TABLE `antrian` (
  `id_antrian` int(11) NOT NULL,
  `id_loket` int(11) NOT NULL,
  `nomor_antrian` varchar(255) NOT NULL,
  `waktu_voice` datetime NOT NULL,
  `waktu_panggil` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `jenis_antrian` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(20) NOT NULL,
  `id_profil` int(20) NOT NULL,
  `hari` varchar(250) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `konsul`
--

CREATE TABLE `konsul` (
  `id_konsul` int(11) NOT NULL,
  `id_loket` int(11) NOT NULL,
  `nama_pengunjung` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_hp` varchar(255) NOT NULL,
  `konsultasi` varchar(255) NOT NULL,
  `tanggal_konsul` datetime NOT NULL,
  `pelayanan_setatus` enum('belum','sudah','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `konsul`
--

INSERT INTO `konsul` (`id_konsul`, `id_loket`, `nama_pengunjung`, `email`, `no_hp`, `konsultasi`, `tanggal_konsul`, `pelayanan_setatus`) VALUES
(1, 1, 'rossa', 'ross132@gmail.com', '092819820129', 'palat motor', '2026-01-01 14:15:31', 'belum'),
(3, 1, 'aldo', 'aldo@gmail.com', '092819820129', 'stnk', '2026-01-01 14:21:11', 'belum'),
(4, 3, 'bero', 'bero22@gmail.com', '08281728618', 'aktifasi akun', '2026-01-01 14:24:56', 'belum'),
(5, 3, 'vekro', 'vekro@gmail.com', '0271281019', 'ngurus perpanjang bpjs', '2026-01-07 00:55:10', 'belum'),
(6, 3, 'vekro', 'vekro@gmail.com', '0271281019', 'faskes', '2026-01-07 12:35:57', 'belum'),
(7, 4, 'vekro', 'vekro@gmail.com', '0271281019', 'ktp', '2026-01-07 12:37:05', 'belum'),
(8, 4, 'vekro', 'vekro@gmail.com', '0271281019', 'ktp', '2026-01-07 12:39:02', 'belum'),
(9, 4, 'vekro', 'vekro@gmail.com', '0271281019', 'kartu keluarga', '2026-01-07 12:39:31', 'belum'),
(10, 4, 'verko', 'vekro@gmail.com', '0271281019', 'ktp', '2026-01-07 12:40:34', 'belum'),
(11, 4, 'verko', 'vekro@gmail.com', '0271281019', 'ngurus kartu kk', '2026-01-07 12:41:28', 'belum'),
(12, 4, 'verko', 'vekro@gmail.com', '0271281019', 'kartu ktp', '2026-01-07 12:42:49', 'belum'),
(13, 1, 'verko', 'vekro@gmail.com', '0271281019', 'stnk', '2026-01-08 06:11:50', 'belum'),
(14, 2, 'verko', 'vekro@gmail.com', '0271281019', 'stnk', '2026-01-08 06:17:58', 'belum'),
(15, 4, 'verko', 'vekro@gmail.com', '0271281019', 'kartu kk', '2026-01-08 06:22:34', 'belum'),
(16, 4, 'verko', 'vekro@gmail.com', '0271281019', 'ktp', '2026-01-08 06:23:02', 'belum'),
(17, 4, 'verko', 'vekro@gmail.com', '0271281019', 'ktp', '2026-01-08 06:25:10', 'belum'),
(18, 1, 'verko', 'vekro@gmail.com', '0271281019', 'ktp', '2026-01-08 06:26:01', 'belum'),
(19, 4, 'verko', 'vekro@gmail.com', '0271281019', 'kartu ktp', '2026-01-08 06:26:53', 'belum'),
(20, 1, 'verko', 'vekro@gmail.com', '0271281019', 'stnk', '2026-01-08 06:27:56', 'belum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `loket`
--

CREATE TABLE `loket` (
  `id_loket` int(11) NOT NULL,
  `nama_loket` varchar(255) NOT NULL,
  `setatus_pelayanan` enum('layanan_penuh','jam_istirahat','buka','tutup') NOT NULL,
  `setatus_loket` enum('buka','tutup','','') NOT NULL,
  `nama_pelayanan` varchar(255) NOT NULL,
  `waktu_terakhir` time NOT NULL,
  `tanggal` date NOT NULL,
  `logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `loket`
--

INSERT INTO `loket` (`id_loket`, `nama_loket`, `setatus_pelayanan`, `setatus_loket`, `nama_pelayanan`, `waktu_terakhir`, `tanggal`, `logo`) VALUES
(1, 'samsat', 'buka', 'buka', 'samsat banjarbaru', '00:00:00', '0000-00-00', 'samsat.jpg'),
(2, 'samsat', 'buka', 'buka', 'samsat kalsel', '00:00:00', '0000-00-00', 'samsat.jpg'),
(3, 'bpjs kesehatan', 'buka', 'buka', 'pelayanan bpjs', '00:00:00', '0000-00-00', 'bpjs_kesehatan.jpg'),
(4, 'DUKCAPIL', 'buka', 'buka', 'pelayanan DUKCAPIL', '00:00:00', '0000-00-00', 'DUKCAPIL.jpg'),
(5, 'DISNAKER', 'buka', 'buka', 'pelayanan DISNAKER', '00:00:00', '0000-00-00', 'DISNAKER.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil`
--

CREATE TABLE `profil` (
  `id_profil` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(255) NOT NULL,
  `status_devisi` varchar(255) NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan','','') NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `id_loket` int(15) NOT NULL,
  `img_user` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil`
--

INSERT INTO `profil` (`id_profil`, `id_user`, `nama_user`, `status_devisi`, `jenis_kelamin`, `tanggal_lahir`, `id_loket`, `img_user`) VALUES
(1, 1, 'aldiy', 'samsat', 'laki-laki', '1996-06-19', 1, 'aldiy.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('BcWiVEG5YFk1VOK7FSskhHW6OhTp5PogplQYYGjM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia1lZa2dNT01XZ202ckRFd2JCNnlkYUR6NGFNMmNKZ1pDMW9vMVdqQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767942153),
('JdYfEzSjiPsOmPrSJxaeAiQls4McXcFqw4zJb3Lp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQTJqamJxREhucHlxNnR4RzdoTHdzaXZuTWJHMWtMeXo1bjl5T2hzMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767940767);

-- --------------------------------------------------------

--
-- Struktur dari tabel `total_antrian`
--

CREATE TABLE `total_antrian` (
  `id_total` int(11) NOT NULL,
  `id_antrian` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah_antrian` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `kategori`) VALUES
(1, 'aldiy', 'aldiy123', 'admin_loket');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `algoritma`
--
ALTER TABLE `algoritma`
  ADD PRIMARY KEY (`id_algoritma`),
  ADD KEY `id_loket` (`id_loket`);

--
-- Indeks untuk tabel `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id_antrian`),
  ADD UNIQUE KEY `id_loket` (`id_loket`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_profil` (`id_profil`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `konsul`
--
ALTER TABLE `konsul`
  ADD PRIMARY KEY (`id_konsul`),
  ADD KEY `id_loket` (`id_loket`);

--
-- Indeks untuk tabel `loket`
--
ALTER TABLE `loket`
  ADD PRIMARY KEY (`id_loket`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id_profil`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_loket` (`id_loket`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `total_antrian`
--
ALTER TABLE `total_antrian`
  ADD PRIMARY KEY (`id_total`),
  ADD UNIQUE KEY `id_antrian` (`id_antrian`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `algoritma`
--
ALTER TABLE `algoritma`
  MODIFY `id_algoritma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id_antrian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `konsul`
--
ALTER TABLE `konsul`
  MODIFY `id_konsul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `loket`
--
ALTER TABLE `loket`
  MODIFY `id_loket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `profil`
--
ALTER TABLE `profil`
  MODIFY `id_profil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `total_antrian`
--
ALTER TABLE `total_antrian`
  MODIFY `id_total` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `algoritma`
--
ALTER TABLE `algoritma`
  ADD CONSTRAINT `algoritma_ibfk_1` FOREIGN KEY (`id_loket`) REFERENCES `loket` (`id_loket`);

--
-- Ketidakleluasaan untuk tabel `antrian`
--
ALTER TABLE `antrian`
  ADD CONSTRAINT `antrian_ibfk_1` FOREIGN KEY (`id_loket`) REFERENCES `loket` (`id_loket`);

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_profil`) REFERENCES `profil` (`id_profil`);

--
-- Ketidakleluasaan untuk tabel `konsul`
--
ALTER TABLE `konsul`
  ADD CONSTRAINT `konsul_ibfk_1` FOREIGN KEY (`id_loket`) REFERENCES `loket` (`id_loket`);

--
-- Ketidakleluasaan untuk tabel `profil`
--
ALTER TABLE `profil`
  ADD CONSTRAINT `profil_ibfk_1` FOREIGN KEY (`id_loket`) REFERENCES `loket` (`id_loket`) ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `total_antrian`
--
ALTER TABLE `total_antrian`
  ADD CONSTRAINT `total_antrian_ibfk_1` FOREIGN KEY (`id_antrian`) REFERENCES `antrian` (`id_antrian`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
