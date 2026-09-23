# 🔍 DEBUG - DATA ANTRIAN TIDAK MASUK KE DATABASE

## 🎯 MASALAH
Data antrian tidak tersimpan ke tabel `antrian` padahal nomor antrian tampil di aplikasi

## 🚀 SOLUSI DEBUGGING

### Step 1: Jalankan Debug Page

Buka di browser:
```
http://localhost:8000/debug-antrian
```

Halaman ini akan menampilkan:
1. ✓ Foreign Key yang terdaftar
2. ✓ Struktur tabel antrian
3. ✓ Data konsul (untuk FK test)
4. ✓ Data loket (untuk FK test)
5. ✓ Test INSERT manual
6. ✓ Total antrian di database
7. ✓ Daftar semua antrian

### Step 2: Analisis Hasil

**Jika foreign key ada:**
```
✓ FK: fk_antrian_konsul
  antrian.id_konsul → konsul.id_konsul
✓ FK: fk_antrian_loket
  antrian.id_loket → loket.id_loket
```

**Jika struktur tabel benar:**
```
Field           Type              Null  Key   Default
id_antrian      bigint(20)        NO    PRI
id_konsul       bigint(20)        NO    MUL
id_loket        bigint(20)        NO    MUL
nomor_antrian   int(11)           NO
waktu_diberikan timestamp         NO
...
```

**Jika test INSERT berhasil:**
```
✓ INSERT BERHASIL! ID: 123
✓ Data terverifikasi:
{
  "id_antrian": 123,
  "id_konsul": 1,
  "id_loket": 1,
  ...
}
```

### Step 3: Troubleshooting Berdasarkan Error

#### Error 1: "No definition found for constraint"
**Penyebab:** Foreign key tidak ada atau tabel belum di-migrate  
**Solusi:**
```bash
php artisan migrate --force
php artisan cache:clear
```

#### Error 2: "Cannot add or update a child row"
**Penyebab:** `id_konsul` atau `id_loket` tidak valid  
**Solusi:**
- Pastikan konsul ada: Check Step 2 poin 3
- Pastikan loket ada: Check Step 2 poin 4

#### Error 3: "Column not found"
**Penyebab:** Kolom `id_konsul` belum ditambahkan ke tabel  
**Solusi:**
```bash
php artisan migrate:rollback
php artisan migrate
```

#### Error 4: INSERT test berhasil tapi form tidak
**Penyebab:** Ada error di KonsulController atau NomorAntrianController  
**Solusi:**
- Check `/storage/logs/laravel.log` untuk error
- Lihat browser console F12 untuk error JavaScript

### Step 4: Test Submit Form

Setelah debug menunjukkan semua ✓:

1. Buka: `http://localhost:8000/konsultasi`
2. Isi form lengkap
3. Submit
4. Buka database atau `/debug-antrian` lagi
5. Harus ada data antrian baru!

---

## 📝 CHECKLIST DEBUGGING

- [ ] Akses `/debug-antrian`
- [ ] Foreign Key ada (2 FK: konsul & loket)
- [ ] Struktur tabel benar (ada id_konsul, id_loket)
- [ ] Data konsul ada (id_konsul ada)
- [ ] Data loket ada (id_loket ada)
- [ ] Test INSERT berhasil
- [ ] Lihat daftar antrian (ada data test)
- [ ] Submit form berhasil
- [ ] Data tampil di database
- [ ] **SELESAI!** ✓

---

## 🔧 QUICK FIX

Jika semua error, coba:

```bash
# 1. Rollback semua migrations
php artisan migrate:rollback --step=10

# 2. Fresh migrate (WARNING: DELETE DATA!)
php artisan migrate:fresh

# 3. Clear cache
php artisan cache:clear
php artisan route:cache

# 4. Composer update
composer dump-autoload

# 5. Test debug lagi
# Buka: http://localhost:8000/debug-antrian
```

---

## 📞 JIKA MASIH STUCK

Screenshot dan kirim:
1. Output dari `/debug-antrian`
2. Error message dari browser/Laravel log
3. SQL query result dari:
   ```sql
   DESCRIBE antrian;
   SELECT * FROM antrian;
   SELECT * FROM konsul LIMIT 1;
   SELECT * FROM loket LIMIT 1;
   ```

---

**Debug Controller:** `DebugAntrianController.php`  
**Debug Route:** `GET /debug-antrian`  
**Status:** ✅ Ready to diagnose
