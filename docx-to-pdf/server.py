from flask import Flask, request, jsonify, json
from docx2pdf import convert
from os.path import exists
import pythoncom

app = Flask(__name__)

@app.route('/convert', methods=['POST'])
def convert_now():
    payload = request.get_json()
    # Pastikan request memiliki file DOCX
    if "file_path" not in payload:
        return jsonify({'error': 'No file_path provided'}), 400

    if not exists(payload['file_path']) :
        return jsonify({'error': 'File not found'}), 404

    # Konversi ke PDF
    pythoncom.CoInitialize()
    convert(payload['file_path'])

    return jsonify({'data': 'success'}), 200

if __name__ == '__main__':
    app.run(debug=True)
