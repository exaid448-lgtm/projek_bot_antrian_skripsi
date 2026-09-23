# 🎫 SISTEM ANTRIAN OTOMATIS - BOT ANTRIAN

## 📌 OVERVIEW

Sistem antrian otomatis yang terintegrasi penuh dengan database untuk mengelola antrian pengunjung secara real-time. Ketika pengunjung mendaftar konsultasi, mereka **otomatis mendapatkan nomor antrian** dan dapat melacak statusnya secara real-time.

---

## ✨ FITUR UTAMA

### 👥 Untuk Pengunjung
- 📋 Form konsultasi dengan pilihan loket & layanan
- 🎫 **Nomor antrian otomatis** saat submit
- 👁️ Tracking status antrian real-time
- 🔄 Auto-refresh status setiap 5 detik
- 📱 Link status antrian mudah diakses

### 🖥️ Untuk Admin Loket
- 📊 Dashboard antrian per loket per hari
- ▶️ Tombol "Mulai" melayani antrian
- ✅ Tombol "Selesai" antrian
- ⏱️ Tracking waktu pelayanan otomatis
- 📈 Persiapan untuk analytics

---

## 🗄️ STRUKTUR DATABASE

### Tabel: `antrian`

```
┌─────────────────────────────────────────────────────────────┐
│ antrian                                                     │
├─────────────────────────────────────────────────────────────┤
│ id_antrian (PK)          │ ID unik antrian                 │
│ id_konsul (FK)           │ Referensi ke tabel konsul       │
│ id_loket (FK)            │ Referensi ke tabel loket        │
│ nomor_antrian (INT)      │ Nomor antrian (001, 002, dst)   │
│ waktu_diberikan (TS)     │ Waktu pemberian (AUTO)          │
│ antrian_mulai (TS NULL)  │ Waktu mulai layanan (AUTO)      │
│ antrian_selesai (TS NULL)│ Waktu selesai layanan (AUTO)    │
│ jenis_antrian (VARCHAR)  │ Tipe antrian (NULL - siap)      │
│ created_at (TS)          │ Timestamp dibuat                 │
│ updated_at (TS)          │ Timestamp diupdate               │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔄 ALUR SISTEM

### 1️⃣ PENGUNJUNG MENDAFTAR
```
Pengunjung
    ↓
Akses /konsultasi
    ↓
Isi form (nama, email, WA, loket, layanan, keperluan)
    ↓
Klik "Kirim Konsultasi"
```

### 2️⃣ SISTEM OTOMATIS MEMPROSES
```
KonsulController::store()
    ↓
Simpan ke tabel 'konsul'
    ↓
Generate nomor antrian (MAX(nomor_antrian) + 1 hari ini)
    ↓
Simpan ke tabel 'antrian' dengan:
  - nomor_antrian = XXX
  - waktu_diberikan = NOW()
  - antrian_mulai = NULL
  - antrian_selesai = NULL
    ↓
Return nomor antrian ke pengunjung
```

### 3️⃣ PENGUNJUNG TRACKING STATUS
```
Pengunjung
    ↓
Klik link "Lihat Status Antrian"
    ↓
Akses /status-antrian/{id}
    ↓
View menampilkan:
  - Status: MENUNGGU / SEDANG DILAYANI / SELESAI
  - Nomor antrian
  - Waktu diberikan
  - Data loket & layanan
    ↓
Auto-refresh setiap 5 detik
```

### 4️⃣ ADMIN MENGELOLA ANTRIAN
```
Admin Login
    ↓
Akses /loket/antrian
    ↓
Lihat tabel antrian hari ini
    ↓
Klik "Mulai" → antrian_mulai = NOW()
    ↓
Layani pengunjung
    ↓
Klik "Selesai" → antrian_selesai = NOW()
    ↓
Pengunjung auto-lihat status berubah
```

---

## 📁 STRUKTUR FILE

```
bot-antrian/
├── app/
│   ├── Models/
│   │   ├── Antrian.php                    ← NEW
│   │   ├── konsultasi.php                 (modified)
│   │   └── loket.php                      (modified)
│   └── Http/Controllers/
│       ├── AntrianController.php           ← NEW
│       └── KonsulController.php            (modified)
├── database/
│   └── migrations/
│       └── 2025_01_16_000003_create_antrian_table.php ← NEW
├── resources/
│   └── views/
│       ├── admin_loket/
│       │   └── antrian.blade.php           ← NEW
│       └── konsul/
│           ├── status_antrian.blade.php    ← NEW
│           └── konsul.blade.php            (modified)
├── routes/
│   └── web.php                             (modified)
├── DEPLOYMENT.md                           ← NEW
├── ANTRIAN_IMPLEMENTATION.md               ← NEW
├── QUERY_TESTING.sql                       ← NEW
├── CHECKLIST.md                            ← NEW
└── FILES_SUMMARY.md                        ← NEW
```

---

## 🚀 QUICK START

### 1. Migration
```bash
php artisan migrate
```

### 2. Test
- Pengunjung: `http://localhost:8000/konsultasi`
- Admin: `http://localhost:8000/loket/antrian` (setelah login)

### 3. Monitor
```bash
# Check data antrian
SELECT * FROM antrian;

# Check per loket hari ini
SELECT l.nama_loket, COUNT(*) FROM antrian a
JOIN loket l ON a.id_loket = l.id_loket
WHERE DATE(a.waktu_diberikan) = CURDATE()
GROUP BY l.nama_loket;
```

---

## 🔗 ROUTES

### Public Routes
| Method | Path | Description |
|--------|------|-------------|
| GET | `/konsultasi` | Form konsultasi pengunjung |
| POST | `/konsultasi/kirim` | Submit form konsultasi |
| GET | `/status-antrian/{id}` | Lihat status antrian |

### Admin Routes (Protected)
| Method | Path | Description |
|--------|------|-------------|
| GET | `/loket/antrian` | Lihat antrian admin |
| GET | `/antrian/{id}/mulai` | Mulai melayani antrian |
| GET | `/antrian/{id}/selesai` | Selesai antrian |

---

## 💾 DATA YANG TERSIMPAN

### Saat Pengunjung Submit:
```json
{
  "id_antrian": 1,
  "id_konsul": 5,
  "id_loket": 2,
  "nomor_antrian": 1,
  "waktu_diberikan": "2025-01-16 10:30:00",
  "antrian_mulai": null,
  "antrian_selesai": null,
  "jenis_antrian": null
}
```

### Saat Admin Klik "Mulai":
```json
{
  "antrian_mulai": "2025-01-16 10:35:15"
}
```

### Saat Admin Klik "Selesai":
```json
{
  "antrian_selesai": "2025-01-16 10:42:50"
}
```

---

## 🔐 KEAMANAN

- ✅ Foreign key constraints (cascade delete)
- ✅ Input validation di controller
- ✅ Exception handling lengkap
- ✅ Auth middleware untuk admin
- ✅ Database transaction safety
- ✅ Null fields aman (tidak ada required constraint yang unnecessary)

---

## 📊 STATISTIC QUERIES

Lihat file `QUERY_TESTING.sql` untuk:
- Total antrian hari ini
- Antrian per loket
- Status summary
- Rata-rata waktu layanan
- Antrian yang menunggu lama

---

## 🎯 FITUR UNTUK KE DEPAN (Framework Sudah Siap)

### Voice Notification ✓
- Field `antrian_mulai` siap untuk trigger
- Bisa integrate TTS library

### SMS/WhatsApp ✓
- Sudah ada `no_hp` dari pengunjung
- Bisa gunakan Twilio/Nexmo

### Priority Queue ✓
- Field `jenis_antrian` ready
- Bisa set "Priority", "Regular", "VIP"

### Analytics ✓
- SQL queries sudah lengkap
- Siap buat dashboard reporting

### Display Board ✓
- Bisa buat halaman terpisah
- Real-time nomor yang dilayani

---

## 📖 DOKUMENTASI LENGKAP

Baca file-file ini untuk detail lebih:

1. **DEPLOYMENT.md** → Panduan instalasi & testing lengkap
2. **CHECKLIST.md** → Checklist implementasi & troubleshooting
3. **ANTRIAN_IMPLEMENTATION.md** → Dokumentasi teknis
4. **QUERY_TESTING.sql** → SQL queries untuk testing & monitoring
5. **FILES_SUMMARY.md** → Ringkas file yang dibuat

---

## 🆘 TROUBLESHOOTING

### Tabel antrian tidak ada?
```bash
php artisan migrate
php artisan cache:clear
```

### Nomor antrian tidak increment?
```sql
SELECT MAX(nomor_antrian) FROM antrian 
WHERE DATE(waktu_diberikan) = CURDATE()
AND id_loket = 2;
```

### Admin tidak bisa akses?
- Check login session
- Verify `auth.custom` middleware active

Lihat `CHECKLIST.md` untuk troubleshooting lengkap.

---

## ✅ STATUS

**Version:** 1.0 Release
**Status:** ✅ PRODUCTION READY
**Tested:** ✅ Fully Functional
**Documentation:** ✅ Complete

---

## 📞 INFO

- **Created:** 16 Januari 2025
- **Framework:** Laravel
- **Database:** MySQL/MariaDB
- **PHP:** 7.4+

---

**Selamat menggunakan Sistem Antrian Otomatis! 🎉**
