import urllib.request
import json

# Test 1: BPJS
url = 'http://127.0.0.1:5500/api/test_detection'
data1 = json.dumps({"text": "saya mau urus bpjs kesehatan"}).encode()
req1 = urllib.request.Request(url, data=data1, headers={'Content-Type': 'application/json'})

try:
    with urllib.request.urlopen(req1) as resp:
        result = json.loads(resp.read().decode())
        print("Test 1 - BPJS:")
        print(json.dumps(result, indent=2))
except Exception as e:
    print(f"ERROR: {e}")
