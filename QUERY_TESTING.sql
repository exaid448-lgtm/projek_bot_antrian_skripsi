-- ==============================================================
-- CONTOH QUERY TESTING SISTEM ANTRIAN
-- ==============================================================

-- 1. LIHAT SEMUA DATA ANTRIAN
SELECT 
    a.id_antrian,
    a.nomor_antrian,
    k.nama_pengunjung,
    k.konsultasi,
    l.nama_loket,
    l.nama_pelayanan,
    a.waktu_diberikan,
    a.antrian_mulai,
    a.antrian_selesai,
    a.jenis_antrian
FROM antrian a
JOIN konsul k ON a.id_konsul = k.id_konsul
JOIN loket l ON a.id_loket = l.id_loket
ORDER BY a.waktu_diberikan DESC;

-- 2. LIHAT ANTRIAN HARI INI
SELECT 
    a.id_antrian,
    a.nomor_antrian,
    k.nama_pengunjung,
    a.waktu_diberikan,
    CASE 
        WHEN a.antrian_selesai IS NOT NULL THEN 'SELESAI'
        WHEN a.antrian_mulai IS NOT NULL THEN 'SEDANG DILAYANI'
        ELSE 'MENUNGGU'
    END AS status
FROM antrian a
JOIN konsul k ON a.id_konsul = k.id_konsul
WHERE DATE(a.waktu_diberikan) = CURDATE()
ORDER BY a.nomor_antrian ASC;

-- 3. LIHAT ANTRIAN PER LOKET HARI INI
SELECT 
    l.id_loket,
    l.nama_loket,
    COUNT(*) as total_antrian,
    SUM(CASE WHEN a.antrian_selesai IS NOT NULL THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN a.antrian_mulai IS NOT NULL AND a.antrian_selesai IS NULL THEN 1 ELSE 0 END) as sedang_dilayani,
    SUM(CASE WHEN a.antrian_mulai IS NULL THEN 1 ELSE 0 END) as menunggu
FROM antrian a
JOIN loket l ON a.id_loket = l.id_loket
WHERE DATE(a.waktu_diberikan) = CURDATE()
GROUP BY l.id_loket, l.nama_loket;

-- 4. LIHAT ANTRIAN YANG MENUNGGU
SELECT 
    a.id_antrian,
    a.nomor_antrian,
    k.nama_pengunjung,
    k.no_hp,
    a.waktu_diberikan,
    TIMESTAMPDIFF(MINUTE, a.waktu_diberikan, NOW()) as lama_menunggu_menit
FROM antrian a
JOIN konsul k ON a.id_konsul = k.id_konsul
WHERE a.antrian_mulai IS NULL
AND DATE(a.waktu_diberikan) = CURDATE()
ORDER BY a.nomor_antrian ASC;

-- 5. LIHAT WAKTU RATA-RATA PELAYANAN
SELECT 
    l.nama_loket,
    COUNT(*) as total_dilayani,
    ROUND(AVG(TIMESTAMPDIFF(MINUTE, a.antrian_mulai, a.antrian_selesai)), 2) as rata_rata_menit
FROM antrian a
JOIN loket l ON a.id_loket = l.id_loket
WHERE a.antrian_mulai IS NOT NULL 
AND a.antrian_selesai IS NOT NULL
AND DATE(a.waktu_diberikan) = CURDATE()
GROUP BY l.nama_loket;

-- 6. LIHAT NOMOR ANTRIAN TERBESAR HARI INI PER LOKET
SELECT 
    a.id_loket,
    l.nama_loket,
    MAX(a.nomor_antrian) as nomor_terakhir
FROM antrian a
JOIN loket l ON a.id_loket = l.id_loket
WHERE DATE(a.waktu_diberikan) = CURDATE()
GROUP BY a.id_loket, l.nama_loket;

-- 7. LIHAT DETAIL SATU ANTRIAN
SELECT 
    a.id_antrian,
    a.nomor_antrian,
    k.nama_pengunjung,
    k.email,
    k.no_hp,
    k.konsultasi,
    l.nama_loket,
    l.nama_pelayanan,
    a.waktu_diberikan,
    a.antrian_mulai,
    a.antrian_selesai,
    a.jenis_antrian
FROM antrian a
JOIN konsul k ON a.id_konsul = k.id_konsul
JOIN loket l ON a.id_loket = l.id_loket
WHERE a.id_antrian = 1; -- Ganti dengan ID antrian yang ingin dicek

-- 8. UPDATE ANTRIAN MULAI (Ketika admin klik "Mulai")
-- UPDATE antrian SET antrian_mulai = NOW() WHERE id_antrian = 1;

-- 9. UPDATE ANTRIAN SELESAI (Ketika admin klik "Selesai")
-- UPDATE antrian SET antrian_selesai = NOW() WHERE id_antrian = 1;

-- 10. HAPUS DATA ANTRIAN (Hanya jika diperlukan)
-- DELETE FROM antrian WHERE id_antrian = 1;
