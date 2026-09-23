#!/usr/bin/env python3
import sys
sys.path.insert(0, 'resources/python_service')

# Patch os.environ untuk mencegah tts errors
import os
os.environ['PYGAME_HIDE_SUPPORT_PROMPT'] = '1'

try:
    import speech_processor as sp
    sp.load_database_config()
    
    # Test classify directly
    print("Test 1: classify_text_from_db('saya mau urus bpjs kesehatan')")
    result = sp.classify_text_from_db('saya mau urus bpjs kesehatan')
    print(f"  Result: {result}\n")
    
    # Now simulate listen_and_process logic
    print("Test 2: Simulating listen_and_process logic")
    loket_name = 'BPJS KESEHATAN'
    text = 'saya mau urus bpjs kesehatan'
    tipe_layanan = 'layanan'
    butuh_lokasi = False
    
    print(f"  loket_name={loket_name}, tipe={tipe_layanan}, butuh={butuh_lokasi}")
    
    # Check route logic
    print(f"[DEBUG] Route logic: butuh_lokasi={butuh_lokasi}, loket_name={loket_name}")
    
    if butuh_lokasi and loket_name == "SAMSAT":
        print("[DEBUG] SAMSAT requires location")
    elif loket_name == "TIDAK_DIKENAL":
        print("[DEBUG] Service not recognized")
    else:
        print("[DEBUG] Service recognized, routing...")
        signal_id = sp.LOKET_SINYAL.get(loket_name, None)
        print(f"[DEBUG] Signal lookup: loket_name={loket_name}, signal_id={signal_id}")
        loket_info = None
        if signal_id:
            try:
                loket_info = sp.get_loket_info_by_id(int(signal_id))
            except Exception as e2:
                print(f"[DEBUG] Exception getting loket info: {e2}")
                loket_info = None

        if loket_info:
            print(f"  Got loket info: {loket_info}")
        else:
            print("  No loket info found")

    print("\nTest completed successfully!")
    
except Exception as e:
    print(f"ERROR: {e}")
    import traceback
    traceback.print_exc()
