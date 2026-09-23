# Sistem Antrian Otomatis - Dokumentasi Implementasi

## 📋 Daftar File yang Dibuat/Dimodifikasi

### File Baru (Dibuat):

1. **database/migrations/2025_01_16_000003_create_antrian_table.php**
   - Migration untuk membuat tabel `antrian`
   - Kolom: `id_antrian`, `id_konsul`, `id_loket`, `nomor_antrian`, `waktu_diberikan`, `antrian_mulai`, `antrian_selesai`, `jenis_antrian`

2. **app/Models/Antrian.php**
   - Model untuk mengelola data antrian
   - Relasi dengan Konsultasi dan Loket

3. **app/Http/Controllers/AntrianController.php**
   - Controller untuk mengelola antrian
   - Method: `index()`, `mulai()`, `selesai()`, `statusPengunjung()`

4. **resources/views/admin_loket/antrian.blade.php**
   - View untuk admin loket melihat daftar antrian
   - Tombol untuk mulai dan selesai antrian

5. **resources/views/konsul/status_antrian.blade.php**
   - View untuk pengunjung melihat status antrian mereka
   - Auto-refresh setiap 5 detik

### File yang Dimodifikasi:

1. **app/Http/Controllers/KonsulController.php**
   - Tambahan: Import model `Antrian`
   - Modifikasi method `store()`: Membuat entry antrian otomatis setelah konsultasi disimpan
   - Generate nomor antrian berdasarkan loket dan tanggal hari ini

2. **app/Models/konsultasi.php**
   - Tambahan: Relasi `antrian()` untuk hasOne Antrian

3. **app/Models/loket.php**
   - Tambahan: Relasi `antrian()` untuk hasMany Antrian

4. **routes/web.php**
   - Import AntrianController
   - Tambahan route `/status-antrian/{id}` (public)
   - Tambahan route `/loket/antrian` (admin)
   - Tambahan route `/antrian/{id}/mulai` (admin)
   - Tambahan route `/antrian/{id}/selesai` (admin)

5. **resources/views/konsul/konsul.blade.php**
   - Tambahan: Link untuk mengecek status antrian setelah berhasil submit

## 🗄️ Struktur Tabel Antrian

```sql
CREATE TABLE antrian (
    id_antrian BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_konsul BIGINT NOT NULL,
    id_loket BIGINT NOT NULL,
    nomor_antrian INT NOT NULL,
    waktu_diberikan TIMESTAMP,
    antrian_mulai TIMESTAMP NULL,
    antrian_selesai TIMESTAMP NULL,
    jenis_antrian VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_konsul) REFERENCES konsul(id_konsul) ON DELETE CASCADE,
    FOREIGN KEY (id_loket) REFERENCES loket(id_loket) ON DELETE CASCADE
);
```

## 🔄 Alur Sistem

### Untuk Pengunjung:
1. Pengunjung mengisi form konsultasi di halaman `/konsultasi`
2. Data disimpan ke tabel `konsul`
3. **Otomatis** membuat entry di tabel `antrian` dengan:
   - Nomor antrian = nomor terakhir hari ini + 1
   - Waktu diberikan = saat ini
4. Pengunjung mendapat notifikasi dengan nomor antrian
5. Pengunjung bisa klik link untuk melihat status antrian real-time

### Untuk Admin Loket:
1. Login ke dashboard
2. Klik menu "Data Antrian" untuk melihat semua antrian hari ini
3. Klik tombol "Mulai" ketika mulai melayani (isi `antrian_mulai`)
4. Klik tombol "Selesai" ketika selesai melayani (isi `antrian_selesai`)

## 🚀 Cara Menjalankan

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Test Sistem
- Akses halaman konsultasi: `http://localhost:8000/konsultasi`
- Isi form dan submit
- Lihat nomor antrian yang diberikan
- Klik link untuk melihat status
- Admin login dan kelola antrian di `/loket/antrian`

## 📊 Data yang Tersimpan

**Contoh data di tabel antrian:**
```
id_antrian: 1
id_konsul: 5
id_loket: 2
nomor_antrian: 1
waktu_diberikan: 2025-01-16 10:30:00
antrian_mulai: NULL (akan diisi saat admin mulai)
antrian_selesai: NULL (akan diisi saat admin selesai)
jenis_antrian: NULL (untuk pengembangan ke depan)
```

## ✨ Fitur yang Tersedia

✅ Nomor antrian otomatis per loket per hari
✅ Waktu pemberian antrian tercatat otomatis
✅ Status antrian real-time (Menunggu, Sedang Dilayani, Selesai)
✅ Pengunjung bisa tracking antrian mereka
✅ Admin bisa manage antrian (mulai/selesai)
✅ Auto-refresh halaman status setiap 5 detik
✅ Field `jenis_antrian` NULL (siap untuk fitur ke depan)

## 🔮 Fitur untuk Pengembangan Ke Depan

1. Set `jenis_antrian` dari admin (misal: "Priority", "Regular", dll)
2. Dashboard real-time display nomor antrian yang sedang dilayani
3. Voice/audio notification saat antrian dipanggil
4. SMS/WA notification ke pengunjung
5. Report dan statistik antrian

---

**Semua file telah siap digunakan. Jalankan `php artisan migrate` untuk membuat tabel di database.**
