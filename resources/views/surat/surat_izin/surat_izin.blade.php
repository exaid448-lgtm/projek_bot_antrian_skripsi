<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Surat Izin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .header {
            background-color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .title {
            margin: 0;
            font-size: 1.25rem;
            color: #1e293b;
            font-weight: 600;
        }

        .subtitle {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
        }

        .btn-close {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-close:hover {
            background-color: #dc2626;
        }

        .viewer-container {
            flex: 1;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #e2e8f0;
            overflow: auto;
        }

        .document-wrapper {
            width: 100%;
            height: 100%;
            max-width: 1000px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
    </style>
    <!-- Menambahkan PDF.js dari CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Mengatur worker PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
</head>
<body>

    <div class="header">
        <div>
            <h2 class="title">📄 Dokumen Surat Izin</h2>
            <p class="subtitle">{{ $filename }}</p>
        </div>
        <button onclick="window.close()" class="btn-close">
            <i class="fa-solid fa-xmark"></i> Tutup Jendela
        </button>
    </div>

    <div class="viewer-container">
        <div class="document-wrapper" id="documentWrapper">
            @php
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $fileUrl = asset('penyimpanan_dokumen/surat_izin/' . $filename);
                $filePath = public_path('penyimpanan_dokumen/surat_izin/' . $filename);
            @endphp

            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                <img src="{{ $fileUrl }}" alt="Surat Izin">
            @elseif($ext == 'pdf')
                @php
                    $base64 = '';
                    if(file_exists($filePath)) {
                        $base64 = base64_encode(file_get_contents($filePath));
                    }
                @endphp
                
                <!-- Kontainer untuk PDF.js -->
                <div id="pdfViewer" style="width: 100%; height: 100%; overflow: auto; display: flex; flex-direction: column; align-items: center; background: #525659; padding: 20px;">
                    <p id="pdfLoading" style="color: white; font-family: 'Poppins';">Memuat PDF, harap tunggu...</p>
                </div>
                
                <script>
                    const pdfViewer = document.getElementById('pdfViewer');
                    const loadingMsg = document.getElementById('pdfLoading');
                    const base64Data = "{{ $base64 }}";

                    if (!base64Data) {
                        loadingMsg.innerText = "Gagal memuat PDF: File tidak ditemukan di server.";
                    } else {
                        try {
                            // Konversi Base64 ke Uint8Array
                            const binaryString = window.atob(base64Data);
                            const bytes = new Uint8Array(binaryString.length);
                            for (let i = 0; i < binaryString.length; i++) {
                                bytes[i] = binaryString.charCodeAt(i);
                            }

                            // Menggunakan PDF.js dengan data langsung, tidak ada request URL yang bisa dibajak IDM
                            pdfjsLib.getDocument({data: bytes}).promise.then(function(pdf) {
                                loadingMsg.style.display = 'none'; // Sembunyikan pesan loading
                                
                                // Loop untuk me-render semua halaman
                                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                                    pdf.getPage(pageNum).then(function(page) {
                                        const scale = 1.5;
                                        const viewport = page.getViewport({ scale: scale });

                                        // Buat canvas untuk setiap halaman
                                        const canvas = document.createElement('canvas');
                                        const ctx = canvas.getContext('2d');
                                        canvas.height = viewport.height;
                                        canvas.width = viewport.width;
                                        canvas.style.marginBottom = '20px';
                                        canvas.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)';

                                        pdfViewer.appendChild(canvas);

                                        const renderContext = {
                                            canvasContext: ctx,
                                            viewport: viewport
                                        };
                                        page.render(renderContext);
                                    });
                                }
                            }).catch(function(error) {
                                loadingMsg.innerText = "Gagal memuat PDF: " + error.message;
                            });
                        } catch (e) {
                            loadingMsg.innerText = "Terjadi kesalahan saat memproses data PDF.";
                        }
                    }
                </script>
            @elseif(in_array($ext, ['doc', 'docx']))
                <div style="text-align: center; padding: 40px; color: #475569;">
                    <i class="fa-solid fa-file-word" style="font-size: 4rem; color: #2563eb; margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 10px;">Dokumen Microsoft Word</h3>
                    <p style="margin-bottom: 20px;">Browser tidak dapat menampilkan dokumen Word secara langsung.<br>File Anda kemungkinan sudah otomatis diunduh oleh browser atau IDM.</p>
                    <a href="{{ $fileUrl }}" download class="btn-close" style="display: inline-flex; background: #2563eb;">
                        <i class="fa-solid fa-download"></i> Unduh Manual
                    </a>
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #475569;">
                    <i class="fa-solid fa-file" style="font-size: 4rem; color: #64748b; margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 10px;">Format Tidak Dikenal</h3>
                    <p style="margin-bottom: 20px;">File ini tidak dapat ditampilkan secara langsung.</p>
                    <a href="{{ $fileUrl }}" download class="btn-close" style="display: inline-flex; background: #2563eb;">
                        <i class="fa-solid fa-download"></i> Unduh File
                    </a>
                </div>
            @endif
        </div>
    </div>

</body>
</html>
