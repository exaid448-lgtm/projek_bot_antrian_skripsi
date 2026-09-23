import sys
sys.path.insert(0, 'resources/python_service')

from speech_processor import load_database_config, classify_text_from_db

print("--- Memuat Konfigurasi Database ---")
load_database_config()

print("\n--- Test Klasifikasi Teks (Lengkap) ---")
test_texts = [
    ("pajak motor banjarbaru", "Should detect SAMASAT + lokasi"),
    ("pajak motor", "Should ask location for SAMSAT"),
    ("ktp", "Should detect DUKCAPIL"),
    ("bpjs kesehatan", "Should detect BPJS KESEHATAN"),
    ("kartu keluarga", "Should detect DUKCAPIL"),
    ("pencari kerja", "Should detect DISNAKER"),  # FIXED: use correct keyword
    ("stnk banjarbaru", "Should detect SAMASAT + lokasi"),
    ("akta lahir", "Should detect DUKCAPIL"),
    ("faskes", "Should detect BPJS KESEHATAN"),
]

for test_text, description in test_texts:
    loket, layanan, butuh_lokasi = classify_text_from_db(test_text)
    status = "[OK]" if loket != "TIDAK_DIKENAL" else "[FAIL]"
    print(f"{status} '{test_text}' -> {loket} (layanan: {layanan}, butuh_lokasi: {butuh_lokasi})")
    print(f"   {description}")
