import mysql.connector

DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'antrian_bot',
    'port': 3307
}

conn = mysql.connector.connect(**DB_CONFIG)
cursor = conn.cursor(dictionary=True)

print('=== SEMUA LOKET ===')
cursor.execute('SELECT id_loket, nama_loket FROM loket')
for row in cursor.fetchall():
    print(f'id_loket={row["id_loket"]}, nama_loket="{row["nama_loket"]}"')

print('\n=== UPPERCASE VERSION ===')
cursor.execute('SELECT id_loket, nama_loket FROM loket')
loket_dict = {}
for row in cursor.fetchall():
    loket_name_upper = row['nama_loket'].upper().strip()
    loket_dict[loket_name_upper] = row
    print(f'Key: "{loket_name_upper}" -> id_loket={row["id_loket"]}')

print(f'\n=== TEST LOOKUP ===')
print(f'Searching for id_loket=1')
found = False
for key, val in loket_dict.items():
    if val['id_loket'] == 1:
        print(f'FOUND: key="{key}", val={val}')
        found = True
if not found:
    print('NOT FOUND!')

conn.close()
