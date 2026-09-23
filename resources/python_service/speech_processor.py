import tempfile
import os
import subprocess
import re
import speech_recognition as sr
import requests
import whisper  # Tambahan untuk akurasi tinggi
import torch
import difflib  # Untuk Fuzzy Matching Koreksi Kata

LARAVEL_API = "http://127.0.0.1:8000/antrian/store-voice"

# Load Model Whisper (Gunakan 'small' untuk keseimbangan kecepatan dan akurasi)
# Model ini akan mendownload data sekitar 460MB saat pertama kali dijalankan
print("Memuat model akurasi tinggi (Whisper)...")
model = whisper.load_model("small")

def normalize(text):
    text = text.lower().strip()
    text = re.sub(r"[^\w\s]", " ", text)
    text = re.sub(r"\s+", " ", text)
    
    # Anti-Gagap (Menghapus kata yang terulang berturut-turut)
    # Contoh: "saya mau ke ke ke loket" -> "saya mau ke loket"
    text = re.sub(r"\b(\w+)( \1\b)+", r"\1", text)
    return text

# Tidak ada class Speaker lagi, TTS dipindah ke JS (Frontend)

def listen_and_process(id_user=None, mode_khusus='0'):
    r = sr.Recognizer()
    tmp_audio_path = None

    try:
        with sr.Microphone() as source:
            # --- OPTIMASI AMBIENT NOISE & MIC BUFFER ---
            print("Mengkalibrasi suara latar...")
            # Kurangi durasi kalibrasi agar pengunjung tidak menunggu kelamaan (dari 1.2s ke 0.8s)
            r.adjust_for_ambient_noise(source, duration=0.8)
            
            # Berikan toleransi energi suara yang lebih dinamis
            r.dynamic_energy_threshold = True 
            
            # Berikan jeda 0.8 detik setelah pengunjung selesai bicara sebelum rekaman benar-benar dipotong
            r.pause_threshold = 0.8 
            
            print("Silakan bicara (Sebutkan layanan Anda)...")
            
            # --- LONGGARKAN TIMEOUT DAN PHRASE LIMIT ---
            # timeout=8: waktu tunggu max sebelum mulai bicara
            # phrase_time_limit=8: durasi max berbicara (8 detik sangat cukup untuk 1 kalimat layanan)
            audio = r.listen(source, timeout=8, phrase_time_limit=8)
            
        with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as tmp_audio:
            tmp_audio.write(audio.get_wav_data())
            tmp_audio_path = tmp_audio.name
                
    except Exception as e:
        print("MIC/RECORD ERROR:", e)
        return {"status": "failed", "message": "Mikrofon bermasalah atau timeout"}

    try:
        print("Memproses transkripsi dengan Whisper...")
        # --- OPTIMASI DECODING WHISPER ---
        result = model.transcribe(
            tmp_audio_path, 
            language="id", 
            initial_prompt="pengurusan, pembuatan, pengubahan data, perpanjangan, pembayaran, cetak kartu, daftar baru, peserta, beserta, hilang, rusak, bpjs kesehatan, stnk, pajak motor, dinkes, dukcapil, taspen, disnaker, banjarbaru",
            temperature=0,
            beam_size=5,
            # Tambahkan parameter ini untuk menahan Whisper agar tidak berhalusinasi saat audio terpotong
            no_speech_threshold=0.6,
            condition_on_previous_text=False
        )
        
        raw_text = result["text"]
        text = normalize(raw_text)
        
        if tmp_audio_path and os.path.exists(tmp_audio_path):
            os.remove(tmp_audio_path)
        
        # --- LOGIKA KOREKSI FUZZY MATCHING (KATA PER KATA) ---
        # Kamus valid/layanan yang diketahui sistem
        valid_words = [
            "pengurusan", "pembuatan", "perpanjangan", "pengubahan", "pembayaran",
            "bpjs", "kesehatan", "samsat", "pajak", "motor", "dinkes", "dukcapil", 
            "data", "banjarbaru", "ketenagakerjaan", "stnk", "konsultasi", "pelayanan",
            "cetak", "kartu", "beserta", "daftar", "baru", "hilang", "rusak", "ktp", "kk",
            "taspen", "disnaker", "pensiunan", "kematian", "akta", "kis", "kuning", "pencari", "kerja"
        ]

        words = text.split()
        corrected_words = []
        for w in words:
            # Lewati kata sandang pendek yang tidak relevan
            if len(w) <= 3 and w not in ["stnk", "bpjs", "kk", "ktp"]:
                continue # Kita buang (drop) kata pendek tak bermakna agar log bersih
            
            # Cari kata dengan kemiripan di atas 75%
            matches = difflib.get_close_matches(w, valid_words, n=1, cutoff=0.75)
            if matches:
                corrected_words.append(matches[0])
            else:
                # JANGAN DIBUANG! Jika admin menambahkan algoritma/kata kunci baru (misal: "taspen"), 
                # kata tersebut harus tetap diteruskan agar bisa dideteksi oleh sistem Rule-Based.
                corrected_words.append(w)
                
        text = " ".join(corrected_words)

        # Tetap sisakan kamus manual untuk perbaikan frasa gabungan jika diperlukan
        kamus_frasa = {
            "perubahan data bekerja": "pengubahan data bpjs kesehatan",
            "kesehatan kesehat": "bpjs kesehatan",
            "data bekerja": "data bpjs",
            "keungsan": "pengurusan",
            "dan membahan": "pengubahan",
            "membahan data": "pengubahan data",
            "basir karta": "beserta cetak kartu"
        }
        for kata_salah, kata_benar in kamus_frasa.items():
            if kata_salah in text:
                text = text.replace(kata_salah, kata_benar)

        print("VOICE (Detected & Corrected):", text)

        # Jika suara kosong / desis saja
        if not text.strip():
            return {"status": "failed", "message": "Suara tidak jelas, silakan ulangi."}

        # --- LOGIKA PENGIRIMAN KE API ---
        try:
            res = requests.post(
                LARAVEL_API,
                json={
                    "loket": text,
                    "id_user": id_user,
                    "mode_khusus": mode_khusus
                },
                headers={"Accept": "application/json"},
                timeout=5
            )
        except requests.exceptions.RequestException as req_err:
            print("API CONNECTION ERROR:", req_err)
            return {"status": "error", "message": "Gagal terhubung ke server antrian."}

        # Cek terlebih dahulu status kode HTTP response dari Laravel
        if res.status_code == 422:
            try:
                data = res.json()
                msg_gagal = data.get("message", "Layanan belum dikenali. Silakan coba kalimat lain.")
            except Exception:
                msg_gagal = "Layanan belum dikenali. Silakan coba kalimat lain."
                
            return {
                "status": "failed",
                "spoken_text": text,
                "message": msg_gagal
            }
            
        # Antisipasi jika ada error server internal lain (500, 404, dll)
        if res.status_code != 200:
            return {"status": "error", "message": "Sistem antrian mengalami gangguan server."}

        # Jika status pasti 200 OK, eksekusi parsing JSON dengan aman
        try:
            data = res.json()
        except Exception as json_err:
            print("JSON PARSE ERROR:", json_err)
            return {"status": "error", "message": "Gagal membaca respon data dari server."}

        # --- LANJUT KE PENGECEKAN DATA ---
        if data.get("butuh_lokasi"):
            return {
                "status": "butuh_lokasi",
                "spoken_text": text,
                "message": data.get("message")
            }

        if data.get("status_pelayanan") == "TUTUP":
            return {
                "status": "closed",
                "status_pelayanan": "tutup",
                "spoken_text": text,
                "loket": data.get("loket"),
                "message": data.get("message"),
                "qr_url": data.get("qr_url")
            }

        if data.get("status") == "butuh_pilihan":
            return {
                "status": "butuh_pilihan",
                "spoken_text": text,
                "pilihan_loket": data.get("pilihan_loket"),
                "message": data.get("message")
            }

        if not data.get("success"):
            return {
                "status": "failed",
                "spoken_text": text,
                "message": data.get("message", "Layanan tidak dikenali")
            }

        nomor = data["nomor"]
        loket = data["loket"]

        # Suara akan diputar oleh browser JS, cukup kirimkan pesannya
        response_message = f"Nomor antrian Anda {nomor}. Silakan menuju loket {loket}."

        return {
            "status": "success",
            "spoken_text": text,
            "loket": loket,
            "nomor": nomor,
            "voice_message": response_message
        }

    except Exception as e:
        print("PROCESS ERROR:", e)
        if tmp_audio_path and os.path.exists(tmp_audio_path):
            os.remove(tmp_audio_path)
        return {"status": "error", "message": "Sistem antrian sedang bermasalah."}