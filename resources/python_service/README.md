# 🤖 ALGORITMA LOKET ANTRIAN - VERSI DATABASE

## 📋 Daftar Isi

1. **[QUICK START](#quick-start)** - Mulai dalam 5 menit
2. **[PERUBAHAN ALGORITMA](#perubahan-algoritma)** - Apa yang berubah
3. **[SETUP LENGKAP](#setup-lengkap)** - Langkah demi langkah
4. **[TROUBLESHOOTING](#troubleshooting)** - Jika ada masalah

---

## 🚀 QUICK START

### Persyaratan:
- MySQL running (port 3307)
- Python 3.8+ installed
- Database `antrian_bot` sudah dibuat

### Setup (5 menit):
```bash
# 1. Navigate ke folder
cd resources/python_service

# 2. Install dependencies
pip install -r requirements.txt

# 3. Setup database (jalankan SQL dari SETUP_GUIDE.md)

# 4. Test koneksi
python speech_processor.py

# 5. Jalankan server
python app.py

# 6. Buka browser
# http://127.0.0.1:5500
```

---

## 🔄 PERUBAHAN ALGORITMA

### SEBELUMNYA (Hardcoded):
```
if "samsat" in text:
    if "banjarbaru" in text:
        loket = "SAMSAT_BANJARBARU"
    elif "kalsel" in text:
        loket = "SAMSAT_KALSEL"
    else:
        loket = "BUTUH_LOKASI"
elif "ktp" in text:
    loket = "DUKCAPIL"
# ... dst
```

**Kelemahan:**
- ❌ Hardcoded, sulit diubah
- ❌ Tidak scalable
- ❌ Perlu restart untuk update

### SEKARANG (Database):
```
algoritma = search_algorithm_in_db(text)
if algoritma.lokasi == 'YA':  # SAMSAT
    if text_has_lokasi:
        loket = specific_samsat_loket
    else:
        loket = "BUTUH_LOKASI"
else:  # Loket lain
    loket = algoritma.loket
```

**Keuntungan:**
- ✅ Dynamic, bisa update database
- ✅ Scalable, unlimited keywords
- ✅ Admin-friendly, no code change needed
- ✅ Database-driven, flexible

---

## 📊 FLOW DIAGRAM

```
┌─────────────────┐
│  User Bicara    │
└────────┬────────┘
         │
         ▼
┌──────────────────────┐
│ Google Speech API    │ → "saya mau urus pajak motor banjarbaru"
└────────┬─────────────┘
         │
         ▼
┌──────────────────────────────┐
│ search_algorithm_in_db()     │
│ SELECT * FROM algoritma      │ → Match: id_loket=1, lokasi='YA'
│ WHERE algoritma LIKE text    │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ Check lokasi column          │
├──────────┬───────────────────┤
│  YA      │     TIDAK/NULL    │
│ (SAMSAT) │  (BPJS, DUKCAPIL) │
└────┬─────┴────┬──────────────┘
     │          │
     ▼          ▼
┌─────────────┐  ┌──────────────┐
│ SAMSAT      │  │ Direct to    │
│ Check lokasi│  │ Loket        │
└──────┬──────┘  └──────────────┘
       │
 ┌─────┴─────┐
 ▼           ▼
SAMSAT_      BUTUH_
BANJARBARU   LOKASI
```

---

## 📚 DOKUMENTASI

| File | Tujuan | Untuk Siapa |
|------|--------|------------|
| **SETUP_GUIDE.md** | Step-by-step setup | Admin/Developer |
| **PERUBAHAN_ALGORITMA.md** | Detail perubahan & logika | Developer/QA |
| **DIAGRAM_ALGORITMA.txt** | Visual flow & comparison | Everyone |
| **FILE_CHANGES_SUMMARY.md** | Perubahan per file | Developer |
| **README.md** (file ini) | Overview & quick start | Everyone |

---

## 🗄️ DATABASE SCHEMA

### Tabel: `loket`
| Column | Type | Keterangan |
|--------|------|-----------|
| id | INT PK | ID loket (1-5) |
| nama_loket | VARCHAR | Nama loket (misal: "SAMSAT BANJARBARU") |
| nama_pelayanan | VARCHAR | Jenis pelayanan (misal: "Pajak Kendaraan") |
| status_pelayanan | VARCHAR | Status (buka/tutup) |
| status_loket | VARCHAR | Status loket |
| waktu_terakhir | TIME | Last update time |
| tanggal | DATE | Last update date |
| logo | VARCHAR | Path ke logo file |

**Data Sample:**
```
ID 1: SAMSAT BANJARBARU | Pajak Kendaraan
ID 2: SAMSAT KALSEL | Pajak Kendaraan
ID 3: BPJS Kesehatan | Kesehatan
ID 4: DUKCAPIL | Kependudukan
ID 5: DISNAKER | Ketenagakerjaan
```

### Tabel: `algoritma`
| Column | Type | Keterangan |
|--------|------|-----------|
| id_algoritma | INT PK | ID record |
| id_loket | INT FK | Reference ke loket(id) |
| algoritma | VARCHAR | Keyword untuk deteksi (misal: "pajak motor banjarbaru") |
| tanggal_dan_waktu | DATETIME | Timestamp |
| tipe_layanan | VARCHAR | Kategori (UMUM, DARURAT, LANSIA) |
| lokasi | VARCHAR | 'YA' jika loket memerlukan lokasi, kosong jika tidak |

**Data Sample:**
```
1: id_loket=1, algoritma="pajak motor banjarbaru", lokasi='YA'
2: id_loket=3, algoritma="bpjs kesehatan", lokasi=NULL
3: id_loket=4, algoritma="ktp akta nikah", lokasi=NULL
4: id_loket=5, algoritma="cari kerja", lokasi=NULL
```

---

## 🔧 KONFIGURASI

### Database Config (`speech_processor.py` line 12-18):
```python
DB_CONFIG = {
    'host': '127.0.0.1',        # Host MySQL
    'user': 'root',              # Username
    'password': '',              # Password
    'database': 'antrian_bot',   # Database name
    'port': 3307                 # Port
}
```

Update sesuai setup MySQL Anda. Cek di `.env` Laravel untuk port yang tepat.

---

## ✅ API ENDPOINTS

### 1. Status Check
```
GET /api/status_check
```
Response:
```json
{
  "status": "ok",
  "message": "Server Flask aktif dan siap",
  "database_loaded": true
}
```

### 2. Process Speech (Real Audio)
```
GET /api/process_speech
```
Response:
```json
{
  "status": "success",
  "loket": "SAMSAT_BANJARBARU",
  "signal": "1",
  "spoken_text": "saya mau urus pajak motor banjarbaru",
  "tipe_layanan": "UMUM",
  "response_text": "Baik, silakan menuju loket satu"
}
```

### 3. Test Detection (Simulasi Text)
```
POST /api/test_detection
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

### 4. Get Config
```
GET /api/config
```
Response:
```json
{
  "loket_data": {...},
  "loket_sinyal": {...},
  "algoritma_count": 26
}
```

---

## 🧪 TESTING

### Test 1: Check Database Connection
```bash
python speech_processor.py
```
✅ Success jika output: "Database configuration berhasil dimuat!"
❌ Error jika: "Error loading database: ..."

### Test 2: Simulate Text Detection
```bash
curl -X POST http://127.0.0.1:5500/api/test_detection \
  -H "Content-Type: application/json" \
  -d '{"text": "saya mau urus pajak motor banjarbaru"}'
```

### Test 3: Check Server Status
```bash
curl http://127.0.0.1:5500/api/status_check
```

### Test 4: Frontend Test
1. Buka http://127.0.0.1:5500
2. Tekan tombol mic
3. Bicara: "Saya mau urus pajak motor Banjarbaru"
4. Lihat output di log

---

## 🐛 TROUBLESHOOTING

### ❌ "mysql.connector module not found"
```bash
pip install mysql-connector-python
```

### ❌ "Access denied for user 'root'"
- Cek username/password di `DB_CONFIG`
- Cek port MySQL (default 3306, tapi bisa 3307)
- Test koneksi: `mysql -h 127.0.0.1 -P 3307 -u root`

### ❌ "Unknown database 'antrian_bot'"
```sql
CREATE DATABASE antrian_bot;
-- Jalankan SQL dari SETUP_GUIDE.md untuk buat tabel
```

### ❌ "No algorithm matched" / Loket always "TIDAK_DIKENAL"
- Cek data di tabel `algoritma` sudah ada
- Verify keyword cocok dengan input text
- Test dengan `/api/test_detection`

### ❌ Server running tapi API timeout
- Cek microphone permission
- Cek network connectivity
- Cek database status

---

## 📝 CHEAT SHEET

### Add New Keyword
```sql
INSERT INTO algoritma (id_loket, algoritma, tipe_layanan, lokasi)
VALUES (1, 'pajak kendaraan bermotor banjarbaru', 'UMUM', 'YA');
```

### Add New Service Type
```sql
INSERT INTO algoritma (id_loket, algoritma, tipe_layanan)
VALUES (3, 'bpjs kategori khusus', 'KHUSUS');
```

### Update Loket Name
```sql
UPDATE loket SET nama_loket = 'SAMSAT BANJARBARU BARU' WHERE id = 1;
```

### View All Data
```sql
SELECT a.*, l.nama_loket 
FROM algoritma a 
JOIN loket l ON a.id_loket = l.id
ORDER BY l.id, a.id_algoritma;
```

### Reset Database
```bash
mysqldump -h 127.0.0.1 -P 3307 -u root antrian_bot > backup.sql
mysql -h 127.0.0.1 -P 3307 -u root antrian_bot < SETUP_SCRIPT.sql
```

---

## 🎯 NEXT STEPS

1. ✅ Follow SETUP_GUIDE.md untuk setup lengkap
2. ✅ Verify dengan test API endpoints
3. ✅ Update database dengan keyword sesuai kebutuhan
4. ✅ Deploy ke production
5. ⏭️ Monitor & maintain

---

## 📞 SUPPORT

Untuk pertanyaan atau issues:
1. Check file dokumentasi yang sesuai
2. Review TROUBLESHOOTING section
3. Cek console log di Flask server
4. Verify database query di MySQL client

---

## 📄 FILE REFERENCE

```
resources/python_service/
├── README.md ←─ Anda di sini
├── SETUP_GUIDE.md ─ Setup step-by-step
├── PERUBAHAN_ALGORITMA.md ─ Detail teknis
├── DIAGRAM_ALGORITMA.txt ─ Flow visual
├── FILE_CHANGES_SUMMARY.md ─ Perubahan file
├── speech_processor.py ─ Core logic (MODIFIED)
├── app.py ─ Flask server (MODIFIED)
└── requirements.txt ─ Dependencies (NEW)
```

---

**Last Updated:** January 2026  
**Version:** 2.0 (Database-driven)  
**Status:** ✅ Ready for Production

