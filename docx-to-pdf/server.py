from flask import Flask, request, jsonify, json
import os
from os.path import exists
import fitz  # PyMuPDF
from PIL import Image
import pytesseract
import subprocess

app = Flask(__name__)

pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
# pytesseract.pytesseract.tesseract_cmd = r'/usr/bin/tesseract'

@app.route('/convert', methods=['POST'])
def convert_now():
    payload = request.get_json()
    # Pastikan request memiliki file DOCX
    if "file_path" not in payload:
        return jsonify({'error': 'No file_path provided'}), 400

    # if not exists(payload['file_path']) :
    #     return jsonify({'error': 'File not found'}), 404

    # Dapatkan path dan nama file tanpa ekstensi
    base_path, filename = os.path.split(payload['file_path'])
    file_name_without_extension, _ = os.path.splitext(filename)

    # Bangun path output PDF dari path input DOCX
    output_pdf = base_path + "/" + file_name_without_extension + ".pdf"

    container_name = 'unoserver'
    infile = '/data/' + payload['file_path']
    outfile = '/data/' + output_pdf
    # print(outfile)
    try:
        # unoconvert --convert-to=pdf /var/www/html/e-surat/backend/storage/app/tmp/surat_keterangan_kerja/1.docx /var/www/html/e-surat/backend/storage/app/tmp/surat_keterangan_kerja/1.pdf
        # Panggil unoconv dari baris perintah untuk mengonversi dokumen

        subprocess.run(['docker', 'exec', container_name , 'unoconvert', '--convert-to=pdf', infile, outfile])
        return jsonify({'data': 'success'}), 200
    except subprocess.CalledProcessError as e:
        print(e)
        return jsonify({'data': e}), 500

def pdf_to_images(pdf_path):
    doc = fitz.open(pdf_path)
    images = []
    for page_number in range(doc.page_count):
        page = doc.load_page(page_number)
        pix = page.get_pixmap()
        img = Image.frombytes("RGB", [pix.width, pix.height], pix.samples)
        images.append(img)
    return images

def extract_text_from_images(images):
    extracted_text = ''
    for img in images:
        text = pytesseract.image_to_string(img)
        extracted_text += text + '\n'  # Add a newline between pages
    return extracted_text

@app.route('/ocr', methods=['POST'])
def ocr():
    payload = request.get_json()
    # Pastikan request memiliki file DOCX
    if "file_path" not in payload:
        return jsonify({'error': 'No file_path provided'}), 400

    if not exists(payload['file_path']) :
        return jsonify({'error': 'File not found'}), 404

    pdf_path = payload['file_path']
    print(pdf_path)
    pdf_images = pdf_to_images(pdf_path)
    # # Extract text from images
    extracted_text = extract_text_from_images(pdf_images)

    return jsonify({'data': extracted_text}), 200

if __name__ == '__main__':
    app.run(debug=True)
