# Dokumentasi Perubahan Algoritma - Database-Driven Classification

## Ringkasan Perubahan

Algoritma klasifikasi loket telah diubah dari **hardcoded** menjadi **berbasis database**. Sistem sekarang mengambil data langsung dari tabel `loket` dan `algoritma` di database MySQL.

---

## Struktur Database yang Diperlukan

### Tabel: `loket`
```sql
CREATE TABLE loket (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_loket VARCHAR(100) NOT NULL,
    nama_pelayanan VARCHAR(100) NOT NULL,
    status_pelayanan VARCHAR(50) DEFAULT 'buka',
    status_loket VARCHAR(50) DEFAULT 'buka',
    waktu_terakhir TIME,
    tanggal DATE,
    logo VARCHAR(255)
);
```

**Contoh data:**
- ID 1: SAMSAT BANJARBARU | Pajak Kendaraan
- ID 2: SAMSAT KALSEL | Pajak Kendaraan
- ID 3: BPJS Kesehatan | Kesehatan
- ID 4: DUKCAPIL | Kependudukan
- ID 5: DISNAKER | Ketenagakerjaan

### Tabel: `algoritma`
```sql
CREATE TABLE algoritma (
    id_algoritma INT PRIMARY KEY AUTO_INCREMENT,
    id_loket INT NOT NULL,
    algoritma VARCHAR(255) NOT NULL,
    tanggal_dan_waktu DATETIME,
    tipe_layanan VARCHAR(100) DEFAULT 'UMUM',
    FOREIGN KEY (id_loket) REFERENCES loket(id)
);
```

**Kolom penting:**
- `id_loket`: Referensi ke tabel loket
- `algoritma`: Keyword/frase untuk pengenalan (contoh: "pajak motor", "ktp", "bpjs")
- `tipe_layanan`: Jenis layanan (UMUM, DARURAT, LANSIA, dll)

---

## Logika Klasifikasi Baru

### 1. **SAMSAT (Memerlukan Lokasi)**
Jika pengguna menyebut "pajak kendaraan" atau keyword SAMSAT tapi TIDAK menyebut lokasi:
- **Respons:** "Mohon sebutkan lokasi Anda: Banjarbaru atau daerah lainnya?"
- **Loket:** `BUTUH_LOKASI` (sinyal = 0)

Jika pengguna menyebut "pajak kendaraan" DAN menyebut lokasi (Banjarbaru/Kalsel):
- **Respons:** "Baik, silakan menuju loket sesuai antrian Anda"
- **Loket:** `SAMSAT_BANJARBARU` atau `SAMSAT_KALSEL` (sinyal = 1 atau 2)

### 2. **Loket Lain (Tidak Memerlukan Lokasi)**
Jika pengguna menyebut keyword yang cocok di database (misal: "ktp", "bpjs", "kerja"):
- **Respons:** Langsung arahkan ke loket dengan antrian
- **Loket:** `DUKCAPIL`, `BPJS`, atau `DISNAKER` (sinyal = 3, 4, atau 5)

### 3. **Tidak Terkenali**
Jika tidak ada keyword yang cocok:
- **Respons:** "Maaf, layanan tidak dikenali"
- **Loket:** `TIDAK_DIKENAL` (sinyal = 0)

---

## File yang Diubah

### 1. **`speech_processor.py`**
- **Ditambahkan:** Koneksi MySQL dan fungsi `load_database_config()`
- **Ditambahkan:** Fungsi `search_algorithm_in_db()` untuk query algoritma
- **Diubah:** `classify_text_from_db()` mengembalikan `(loket_name, tipe_layanan, butuh_lokasi)`
- **Diubah:** `listen_and_process()` menggunakan logika baru berbasis database
- **Import baru:** `mysql.connector`

### 2. **`app.py`**
- **Ditambahkan:** Pemanggilan `sp.load_database_config()` saat startup
- **Diubah:** `/api/process_speech` mengembalikan `tipe_layanan` di response
- **Diubah:** `/api/test_detection` menerima testing berbasis database
- **Diubah:** `/api/config` menampilkan data dari database

### 3. **`requirements.txt` (Baru)**
- Menambahkan dependency: `mysql-connector-python==8.2.0`

---

## Konfigurasi Database

Edit `speech_processor.py` baris 13-18 sesuai konfigurasi MySQL Anda:

```python
DB_CONFIG = {
    'host': '127.0.0.1',      # Host database
    'user': 'root',           # Username
    'password': '',           # Password
    'database': 'antrian_bot', # Nama database
    'port': 3307              # Port MySQL (sesuai .env)
}
```

---

## Testing

### Test di Terminal Python:
```bash
cd resources/python_service
python speech_processor.py
```

Outputnya:
```
--- Memuat Konfigurasi Database ---
✅ Database configuration berhasil dimuat!
   Loket ditemukan: ['SAMSAT_BANJARBARU', 'SAMSAT_KALSEL', 'BPJS', 'DUKCAPIL', 'DISNAKER']

--- Test Klasifikasi Teks (dari Database) ---
Input: 'saya mau urus pajak motor banjarbaru'
  -> Loket: SAMSAT_BANJARBARU, Layanan: UMUM, Butuh Lokasi: False

Input: 'saya mau urus ktp aja'
  -> Loket: DUKCAPIL, Layanan: UMUM, Butuh Lokasi: False

Input: 'saya perlu informasi bpjs kesehatan'
  -> Loket: BPJS, Layanan: UMUM, Butuh Lokasi: False

Input: 'saya mau cari kerja di disnaker'
  -> Loket: DISNAKER, Layanan: UMUM, Butuh Lokasi: False
```

### Test dengan API:
```bash
# Test dengan teks simulasi
curl -X POST http://127.0.0.1:5500/api/test_detection \
  -H "Content-Type: application/json" \
  -d '{"text": "saya mau urus pajak motor banjarbaru"}'

# Response:
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

---

## Penambahan Data di Database

Untuk menambah keyword baru di database:

```sql
INSERT INTO algoritma (id_loket, algoritma, tipe_layanan)
VALUES (1, 'pajak motor banjarbaru', 'UMUM');

INSERT INTO algoritma (id_loket, algoritma, tipe_layanan)
VALUES (4, 'ktp akta nikah', 'UMUM');

INSERT INTO algoritma (id_loket, algoritma, tipe_layanan)
VALUES (3, 'bpjs jkn kis', 'UMUM');
```

---

## Fallback Mode

Jika database tidak terkoneksi, sistem akan otomatis menggunakan **hardcoded fallback** yang ada di `setup_fallback_config()`. Log akan menampilkan:
```
❌ Error loading database: [error message]
   Menggunakan fallback hardcoded config...
```

Ini memastikan sistem tetap berjalan meski ada masalah dengan database.

---

## Response Perubahan dari API

### Sebelumnya:
```json
{
  "loket": "SAMSAT_BANJARBARU",
  "signal": "1",
  "spoken_text": "...",
  "response_text": "..."
}
```

### Sesudahnya:
```json
{
  "loket": "SAMSAT_BANJARBARU",
  "signal": "1",
  "spoken_text": "...",
  "tipe_layanan": "UMUM",
  "response_text": "..."
}
```

Tambahan kolom `tipe_layanan` untuk keperluan sistem antrian yang lebih detail.

---

## Keuntungan

✅ **Fleksibel:** Menambah/mengubah algoritma tanpa perlu edit code  
✅ **Scalable:** Mudah mengelola banyak kategori layanan  
✅ **Dynamic:** Database real-time bisa diubah admin tanpa restart  
✅ **Maintainable:** Logika terpisah dari data  
✅ **Robust:** Ada fallback jika database tidak terkoneksi  

---

## Troubleshooting

### Error: "mysql.connector not found"
```bash
pip install mysql-connector-python
```

### Error: "Access denied for user 'root'@'127.0.0.1'"
- Cek username/password di `DB_CONFIG` di `speech_processor.py`
- Pastikan service MySQL berjalan

### Error: "Unknown database 'antrian_bot'"
- Pastikan database dan tabel sudah dibuat
- Cek port MySQL (default 3306, tapi project ini menggunakan 3307)

