import sys
sys.path.insert(0, 'resources/python_service')
import speech_processor as sp

sp.load_database_config()

# Mock the speech recognition part
class MockAudio:
    pass

class MockRecognizer:
    def recognize_google(self, audio, language=None):
        return "saya mau urus bpjs kesehatan"
    
    def adjust_for_ambient_noise(self, mic, duration=None):
        pass
    
    def listen(self, mic, timeout=None, phrase_time_limit=None):
        return MockAudio()

# Replace recognizer
import speech_recognition as sr
original_recognizer = sr.Recognizer

sr.Recognizer = lambda: MockRecognizer()
sr.Microphone = lambda: None

# Now test
try:
    result = sp.listen_and_process()
    print("Success!")
    print(result)
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
