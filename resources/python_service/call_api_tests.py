import json
from urllib import request

url = 'http://127.0.0.1:5500/api/test_detection'

tests = [
    'saya mau urus pajak motor banjarbaru',
    'saya mau urus pajak motor',
    'saya mau urus bpjs kesehatan'
]

for t in tests:
    data = json.dumps({'text': t}).encode('utf-8')
    req = request.Request(url, data=data, headers={'Content-Type': 'application/json'})
    try:
        with request.urlopen(req, timeout=5) as resp:
            print(resp.read().decode('utf-8'))
    except Exception as e:
        print('ERROR calling API:', e)
    print('---')
