#!/usr/bin/env python3
import urllib.request
import json

url = 'http://127.0.0.1:5500/api/test_detection'
text = "saya mau urus bpjs kesehatan"
data = json.dumps({"text": text}).encode()

req = urllib.request.Request(url, data=data, headers={'Content-Type': 'application/json'})
try:
    with urllib.request.urlopen(req, timeout=10) as resp:
        result = json.loads(resp.read().decode())
        print(json.dumps(result, indent=2))
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
