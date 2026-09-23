import mysql.connector
import pandas as pd
import pickle
import os
import sys
import time
from datetime import datetime
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.svm import SVC
from sklearn.pipeline import make_pipeline
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score

# Catat waktu mulai
start_time = time.time()

# Ambil argumen algoritma (default Naive Bayes)
algorithm_choice = sys.argv[1] if len(sys.argv) > 1 else 'Naive Bayes'

# Konfigurasi Database (Sebaiknya gunakan env di production, ini untuk prototype)
db_port = 3307
db_name = "antrian_bot"

try:
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database=db_name,
        port=db_port
    )
except Exception as e:
    print(f"ERROR|Gagal koneksi database: {str(e)}")
    sys.exit(1)

cursor = db.cursor()

# Ambil data latih
query = "SELECT teks_transkripsi, id_loket FROM voice_training"
df = pd.read_sql(query, db)

# Fungsi pembersihan teks
def clean_text(text):
    import re
    if text is None:
        return ""
    text = text.lower()
    text = re.sub(r'[^a-zA-Z\s]', ' ', text)
    text = re.sub(r'\b(\w+)( \1\b)+', r'\1', text) # Hapus kata berulang
    text = re.sub(r'\s+', ' ', text).strip()
    return text

df['teks_transkripsi'] = df['teks_transkripsi'].apply(clean_text)

if not df.empty and len(df) > 1:
    X = df['teks_transkripsi']
    y = df['id_loket']
    
    # Train-test split (80% train, 20% test)
    # Jika data terlalu sedikit dan test_size error, pakai semua data
    try:
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
    except ValueError:
        X_train, X_test, y_train, y_test = X, X, y, y

    # Pilih Algoritma berdasarkan input web
    if algorithm_choice == 'SVM':
        classifier = SVC(kernel='linear', probability=True)
    else:
        classifier = MultinomialNB()
        algorithm_choice = 'Naive Bayes' # fallback naming
        
    model = make_pipeline(TfidfVectorizer(), classifier)
    
    # Latih model
    model.fit(X_train, y_train)
    
    # Hitung Akurasi
    y_pred = model.predict(X_test)
    accuracy = accuracy_score(y_test, y_pred) * 100
    
    # ATOMIC SAVE: Simpan ke file temp dulu, lalu rename.
    # Mencegah error jika predict.py dipanggil bertepatan dengan training
    script_dir = os.path.dirname(os.path.abspath(__file__))
    temp_filename = os.path.join(script_dir, 'model_mpp_temp.pkl')
    final_filename = os.path.join(script_dir, 'model_mpp.pkl')
    
    with open(temp_filename, 'wb') as f:
        pickle.dump(model, f)
        
    os.replace(temp_filename, final_filename)
    
    end_time = time.time()
    exec_time = round(end_time - start_time, 2)
    exec_time_str = f"{exec_time} detik"
    
    # Simpan laporan ke tabel riwayat_training
    jumlah_data = len(df)
    now_str = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    
    insert_query = """
        INSERT INTO riwayat_training (algoritma_dipakai, akurasi, jumlah_data, waktu_eksekusi, created_at, updated_at) 
        VALUES (%s, %s, %s, %s, %s, %s)
    """
    cursor.execute(insert_query, (algorithm_choice, float(accuracy), int(jumlah_data), exec_time_str, now_str, now_str))
    db.commit()
    
    print(f"SUCCESS|Akurasi: {accuracy:.2f}%|Waktu: {exec_time_str}|Algoritma: {algorithm_choice}")
else:
    print("ERROR|Data kosong atau terlalu sedikit, isi tabel voice_training minimal 2 baris!")
    
db.close()