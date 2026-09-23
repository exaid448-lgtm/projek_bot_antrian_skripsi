import json
import speech_processor as sp

print('Loading DB config...')
sp.load_database_config()

tests = [
    'saya mau urus pajak motor banjarbaru',
    'saya mau urus pajak motor',
    'saya mau urus bpjs kesehatan',
    'saya mau urus ktp',
    'saya mau cari kerja di disnaker',
]

for t in tests:
    loket, tipe, butuh = sp.classify_text_from_db(t)
    signal = sp.LOKET_SINYAL.get(loket, '0')
    print(json.dumps({
        'input': t,
        'loket': loket,
        'signal': signal,
        'tipe_layanan': tipe,
        'butuh_lokasi': butuh
    }, ensure_ascii=False))
