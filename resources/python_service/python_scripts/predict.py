import pickle
import sys
import os
import re

# Pastikan path model benar
model_path = 'model_mpp.pkl'
script_dir = os.path.dirname(os.path.abspath(__file__))
full_model_path = os.path.join(script_dir, model_path)

if not os.path.exists(full_model_path):
    print("ERROR|Model belum dilatih. Harap jalankan training data terlebih dahulu.")
    sys.exit(1)

if len(sys.argv) < 2:
    print("ERROR|Tidak ada teks yang dikirim.")
    sys.exit(1)

input_teks = sys.argv[1]

# Pembersihan dasar (biar sama dengan saat training)
input_teks = input_teks.lower()
input_teks = re.sub(r'[^a-zA-Z\s]', ' ', input_teks)
input_teks = re.sub(r'\b(\w+)( \1\b)+', r'\1', input_teks)
input_teks = re.sub(r'\s+', ' ', input_teks).strip()

try:
    with open(full_model_path, 'rb') as f:
        model = pickle.load(f)
        
    hasil = model.predict([input_teks])
    print(f"SUCCESS|{hasil[0]}")
except Exception as e:
    print(f"ERROR|Gagal memprediksi: {str(e)}")