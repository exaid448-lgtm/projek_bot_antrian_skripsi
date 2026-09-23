# PANDUAN SETUP & IMPLEMENTASI ALGORITMA BERBASIS DATABASE

## Step 1: Persiapan Database

### A. Pastikan MySQL Running
```bash
# Windows - Check service
sc query MySQL

# Jika tidak running, start:
net start MySQL
```

### B. Koneksi ke Database
```bash
mysql -h 127.0.0.1 -P 3307 -u root
# Atau buka phpMyAdmin di http://localhost/phpmyadmin
```

### C. Buat Database (jika belum ada)
```sql
CREATE DATABASE IF NOT EXISTS antrian_bot;
USE antrian_bot;
```

### D. Buat Tabel loket
```sql
CREATE TABLE `loket` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama_loket` varchar(100) NOT NULL,
  `nama_pelayanan` varchar(100) NOT NULL,
  `status_pelayanan` varchar(50) DEFAULT 'buka',
  `status_loket` varchar(50) DEFAULT 'buka',
  `waktu_terakhir` time DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### E. Insert Data Loket
```sql
INSERT INTO `loket` (`nama_loket`, `nama_pelayanan`, `status_pelayanan`, `status_loket`) VALUES
('SAMSAT BANJARBARU', 'Pajak Kendaraan', 'buka', 'buka'),
('SAMSAT KALSEL', 'Pajak Kendaraan', 'buka', 'buka'),
('BPJS Kesehatan', 'Kesehatan', 'buka', 'buka'),
('DUKCAPIL', 'Kependudukan', 'buka', 'buka'),
('DISNAKER', 'Ketenagakerjaan', 'buka', 'buka');
```

### F. Buat Tabel algoritma
```sql
CREATE TABLE `algoritma` (
  `id_algoritma` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_loket` int NOT NULL,
  `algoritma` varchar(255) NOT NULL,
  `tanggal_dan_waktu` datetime DEFAULT CURRENT_TIMESTAMP,
  `tipe_layanan` varchar(100) DEFAULT 'UMUM',
  `lokasi` varchar(100) DEFAULT NULL,
  FOREIGN KEY (`id_loket`) REFERENCES `loket`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### G. Insert Data Algoritma
```sql
-- SAMSAT BANJARBARU (dengan lokasi)
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`, `lokasi`) VALUES
(1, 'pajak motor banjarbaru', 'UMUM', 'YA'),
(1, 'pajak mobil banjarbaru', 'UMUM', 'YA'),
(1, 'stnk banjarbaru', 'UMUM', 'YA'),
(1, 'pajak kendaraan banjarbaru', 'UMUM', 'YA');

-- SAMSAT KALSEL (dengan lokasi)
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`, `lokasi`) VALUES
(2, 'pajak motor banjarmasin', 'UMUM', 'YA'),
(2, 'pajak kendaraan kalsel', 'UMUM', 'YA'),
(2, 'pajak motor kandangan', 'UMUM', 'YA'),
(2, 'pajak kendaraan tapin', 'UMUM', 'YA');

-- BPJS (tanpa lokasi)
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`) VALUES
(3, 'bpjs kesehatan', 'UMUM'),
(3, 'bpjs jkn', 'UMUM'),
(3, 'bpjs kis', 'UMUM'),
(3, 'kartu sehat', 'UMUM');

-- DUKCAPIL (tanpa lokasi)
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`) VALUES
(4, 'ktp elektronik', 'UMUM'),
(4, 'kartu keluarga', 'UMUM'),
(4, 'akta lahir', 'UMUM'),
(4, 'akta nikah', 'UMUM'),
(4, 'surat pindah', 'UMUM');

-- DISNAKER (tanpa lokasi)
INSERT INTO `algoritma` (`id_loket`, `algoritma`, `tipe_layanan`) VALUES
(5, 'cari kerja', 'UMUM'),
(5, 'lowongan kerja', 'UMUM'),
(5, 'kartu kuning', 'UMUM'),
(5, 'sertifikat kerja', 'UMUM');
```

### H. Verifikasi Data
```sql
-- Lihat semua data
SELECT * FROM loket;
SELECT * FROM algoritma;

-- Cek foreign key
SELECT a.*, l.nama_loket 
FROM algoritma a 
JOIN loket l ON a.id_loket = l.id;
```

---

## Step 2: Setup Python Environment

### A. Navigate ke folder Python Service
```bash
cd resources/python_service
```

### B. Buat Virtual Environment (Optional tapi Recommended)
```bash
# Buat venv
python -m venv venv

# Activate venv
# Windows CMD:
venv\Scripts\activate
# atau Windows PowerShell:
venv\Scripts\Activate.ps1
```

### C. Install Dependencies
```bash
pip install -r requirements.txt
```

### D. Verifikasi Instalasi
```bash
python -c "import mysql.connector; print('MySQL connector OK')"
python -c "import flask; print('Flask OK')"
python -c "import speech_recognition; print('SpeechRecognition OK')"
```

---

## Step 3: Update Konfigurasi (Jika Perlu)

Edit `speech_processor.py` baris 12-18:

```python
DB_CONFIG = {
    'host': '127.0.0.1',        # Sesuai host MySQL Anda
    'user': 'root',              # Username MySQL
    'password': '',              # Password MySQL (kosong jika tidak ada)
    'database': 'antrian_bot',   # Nama database
    'port': 3307                 # Port MySQL (sesuai .env Laravel)
}
```

---

## Step 4: Test Koneksi Database

### A. Test via Python Script
```bash
python speech_processor.py
```

**Output Berhasil:**
```
--- Memuat Konfigurasi Database ---
✅ Database configuration berhasil dimuat!
   Loket ditemukan: ['SAMSAT_BANJARBARU', 'SAMSAT_KALSEL', 'BPJS', 'DUKCAPIL', 'DISNAKER']

--- Test Klasifikasi Teks (dari Database) ---
Input: 'saya mau urus pajak motor banjarbaru'
  -> Loket: SAMSAT_BANJARBARU, Layanan: UMUM, Butuh Lokasi: False
...
```

**Output Error - Fallback Mode:**
```
❌ Error loading database: Error connecting to MySQL Server...
   Menggunakan fallback hardcoded config...
```

---

## Step 5: Jalankan Server Flask

### A. Start Server
```bash
python app.py
```

**Output:**
```
==========================================
🚀 SERVER LOKET ANTRIAN OTOMATIS AKTIF
🌐 Akses Web: http://127.0.0.1:5500
==========================================

🔄 Memload konfigurasi dari database...
✅ Database configuration berhasil dimuat!
==========================================
```

### B. Test API via Browser / Postman

**1. Check Status:**
```
GET http://127.0.0.1:5500/api/status_check
```

Response:
```json
{
  "status": "ok",
  "message": "Server Flask aktif dan siap",
  "database_loaded": true
}
```

**2. Test Deteksi Teks:**
```
POST http://127.0.0.1:5500/api/test_detection
Content-Type: application/json

{
  "text": "saya mau urus pajak motor banjarbaru"
}
```

Response:
```json
{
  "status": "success",
  "data": {
    "input_text": "saya mau urus pajak motor banjarbaru",
    "loket": "SAMSAT_BANJARBARU",
    "signal": "1",
    "tipe_layanan": "UMUM",
    "butuh_lokasi": false
  }
}
```

**3. Get Config (Debug):**
```
GET http://127.0.0.1:5500/api/config
```

---

## Step 6: Test via Frontend

1. Buka http://127.0.0.1:5500 (atau akses sesuai web server Laravel Anda)
2. Tekan tombol mic dan bicara
3. Lihat output di log dan simulasi loket

---

## TROUBLESHOOTING

### ❌ Error: "mysql.connector module not found"
```bash
pip install mysql-connector-python
```

### ❌ Error: "Access denied for user 'root'@'127.0.0.1:3307'"
- Cek username/password di `DB_CONFIG`
- Verifikasi port MySQL (cek di .env atau phpMyAdmin)
- Cek user MySQL sudah di-setup dengan benar

### ❌ Error: "Unknown database 'antrian_bot'"
```sql
-- Buat database
CREATE DATABASE antrian_bot;

-- Pastikan tabel sudah dibuat dan ada data
SHOW TABLES;
```

### ❌ Server berjalan tapi API timeout
- Cek database sudah terkoneksi: `GET /api/config`
- Cek microphone sudah aktif & permission diberikan
- Lihat error di console Flask server

### ❌ Algoritma tidak terdeteksi saat bicara
- Verifikasi keyword di tabel `algoritma` cocok dengan input
- Test dengan `/api/test_detection` dulu
- Tambah debug: `print()` di `speech_processor.py`

---

## MEMAINTAIN SISTEM

### A. Tambah Keyword Baru
```sql
INSERT INTO algoritma (id_loket, algoritma, tipe_layanan, lokasi)
VALUES (3, 'bpjs kesehatan nasional', 'UMUM', NULL);
```

### B. Ubah Loket
```sql
UPDATE loket 
SET nama_pelayanan = 'Pajak Kendaraan Bermotor'
WHERE id = 1;
```

### C. Backup Database
```bash
mysqldump -h 127.0.0.1 -P 3307 -u root antrian_bot > backup.sql
```

### D. Restore Database
```bash
mysql -h 127.0.0.1 -P 3307 -u root antrian_bot < backup.sql
```

---

## CHECKLIST FINAL

- [ ] Database MySQL running di port 3307
- [ ] Database `antrian_bot` sudah dibuat
- [ ] Tabel `loket` dan `algoritma` sudah dibuat dengan data
- [ ] Python 3.8+ sudah installed
- [ ] Dependencies di `requirements.txt` sudah installed
- [ ] DB_CONFIG di `speech_processor.py` sesuai
- [ ] `python speech_processor.py` berhasil (tidak error)
- [ ] `python app.py` server berjalan
- [ ] API `/api/status_check` respons OK
- [ ] API `/api/test_detection` deteksi teks dengan benar
- [ ] Frontend mic test berfungsi (jika ada hardware)

---

## NEXT STEPS

1. **Monitoring:** Setup logging untuk track semua request
2. **Admin Panel:** Buat UI untuk manage algoritma & loket
3. **Analytics:** Track statistik pengunjung per loket
4. **Optimization:** Cache database config, optimize query
5. **Multi-Language:** Support bahasa lain selain Bahasa Indonesia

