import logging
from flask import Flask, jsonify, request
from flask_cors import CORS
import speech_processor as sp

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)

@app.route('/api/process_speech', methods=['GET'])
def process_speech():
    # Tangkap id_user dari request browser
    id_user = request.args.get('id_user', None)
    mode_khusus = request.args.get('mode_khusus', '0')
    logger.info(f"🎤 Request suara diterima untuk User ID: {id_user}, Mode Khusus: {mode_khusus}")
    try:
        # Kirim id_user ke fungsi processor
        result = sp.listen_and_process(id_user, mode_khusus)
        return jsonify(result), 200
    except Exception as e:
        logger.error(e)
        return jsonify({
            "status": "error",
            "message": "Flask error"
        }), 500

if __name__ == '__main__':
    print("🚀 Flask Voice Server running di http://127.0.0.1:5500")
    app.run(host="0.0.0.0", port=5500, debug=False)
