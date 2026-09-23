# 📋 REFACTORING ANTRIAN - DOKUMENTASI

## ✅ YANG DIUBAH

Logic generate nomor antrian yang sebelumnya berada di `KonsulController.php` telah **DIPISAH** ke file tersendiri: **`AntrianPengunjung.php`**

---

## 📁 FILE YANG BERUBAH

### File Baru:
```
app/Models/AntrianPengunjung.php (NEW)
```

### File Dimodifikasi:
```
app/Http/Controllers/KonsulController.php (UPDATED)
```

---

## 🎯 CLASS ANTRIAN PENGUNJUNG

**Lokasi:** `app/Models/AntrianPengunjung.php`

**Method yang tersedia:**

### 1. generateNomorAntrian($id_loket)
Generate nomor antrian otomatis untuk loket hari ini
```php
$nomor = AntrianPengunjung::generateNomorAntrian($id_loket);
// Returns: 1, 2, 3, ... (per loket per hari)
```

### 2. buatAntrian($id_konsul, $id_loket)
Buat entry antrian baru lengkap
```php
$antrian = AntrianPengunjung::buatAntrian($konsulId, $loketId);
// Returns: Antrian model dengan nomor otomatis
```

### 3. formatNomorAntrian($nomor)
Format nomor dengan padding (001, 002, dst)
```php
$formatted = AntrianPengunjung::formatNomorAntrian(1);
// Returns: "001"
```

### 4. getNomorAntrianTerakhir($id_loket)
Ambil nomor antrian terakhir untuk loket hari ini
```php
$terakhir = AntrianPengunjung::getNomorAntrianTerakhir($id_loket);
// Returns: 5 (atau null jika tidak ada)
```

### 5. hitungAntrianMenunggu($id_loket)
Hitung total antrian yang menunggu
```php
$menunggu = AntrianPengunjung::hitungAntrianMenunggu($id_loket);
// Returns: 3
```

---

## 🔄 SEBELUM vs SESUDAH

### SEBELUM (KonsulController.php - Tanpa Refactor)
```php
// 4. Generate nomor antrian otomatis berdasarkan loket dan tanggal hari ini
$tanggalHari = now()->toDateString();
$nomorAntrianTerakhir = DB::table('antrian')
    ->whereDate('waktu_diberikan', $tanggalHari)
    ->where('id_loket', $dataLoket->id_loket)
    ->max('nomor_antrian') ?? 0;

$nomorAntrianBaru = $nomorAntrianTerakhir + 1;

// 5. Insert ke tabel 'antrian'
$antrian = Antrian::create([
    'id_konsul'       => $konsulId,
    'id_loket'        => $dataLoket->id_loket,
    'nomor_antrian'   => $nomorAntrianBaru,
    'waktu_diberikan' => now(),
]);

// Format nomor
$formatted = str_pad($nomorAntrianBaru, 3, '0', STR_PAD_LEFT);
```

### SESUDAH (KonsulController.php - Dengan Refactor)
```php
// 4. Buat antrian menggunakan class AntrianPengunjung
$antrian = AntrianPengunjung::buatAntrian($konsulId, $dataLoket->id_loket);

// 5. Format nomor antrian untuk ditampilkan
$nomorAntrianFormatted = AntrianPengunjung::formatNomorAntrian($antrian->nomor_antrian);
```

**Lebih clean & mudah dibaca!** ✅

---

## 💡 KEUNTUNGAN REFACTORING

✅ **Clean Code** - Logic terpisah, lebih mudah dibaca  
✅ **Reusable** - Bisa dipanggil dari mana saja  
✅ **Maintainable** - Mudah diubah/update di satu tempat  
✅ **Testable** - Bisa di-unit test sendiri  
✅ **Extensible** - Bisa tambah method baru  

---

## 🚀 CARA MENGGUNAKAN

### Di Controller:
```php
use App\Models\AntrianPengunjung;

// Generate nomor
$nomor = AntrianPengunjung::generateNomorAntrian(1);

// Buat antrian lengkap
$antrian = AntrianPengunjung::buatAntrian($konsulId, $loketId);

// Format untuk tampilan
$formatted = AntrianPengunjung::formatNomorAntrian($nomor);
```

### Di Helper/Blade Template:
```blade
{{ AntrianPengunjung::formatNomorAntrian($antrian->nomor_antrian) }}
```

---

## 📊 STRUKTUR FILE

```
app/Models/
├── Antrian.php (existing)
├── AntrianPengunjung.php (NEW) ← Logic untuk generate antrian
├── konsultasi.php (existing)
└── loket.php (existing)

app/Http/Controllers/
└── KonsulController.php (UPDATED) ← Sekarang lebih clean
```

---

## ✨ YANG TETAP SAMA

✅ Database struktur tidak berubah  
✅ Functionality tetap sama  
✅ Output nomor antrian tetap sama  
✅ Routes tidak berubah  
✅ Views tidak berubah  

---

## 🔧 FUTURE EXTENSION

Sekarang gampang tambah method baru di `AntrianPengunjung`:

```php
// Contoh method yang bisa ditambah:

public static function getNomorSekarangDilayani($id_loket) { }
public static function getAntrianByStatus($id_loket, $status) { }
public static function resetAntrianHariBaru() { }
public static function getStatistikAntrian($id_loket, $date) { }
```

---

## ✅ SUMMARY

**Refactoring completed!** 

Logic `generateNomorAntrian` sekarang:
- ✅ Berada di file tersendiri: `AntrianPengunjung.php`
- ✅ Dapat digunakan ulang di berbagai tempat
- ✅ Lebih mudah di-maintain
- ✅ KonsulController.php jadi lebih clean

Semua fitur tetap berjalan normal! 🎉

---

**Version:** 1.0.1 (Refactored)  
**Date:** 16 Januari 2025
