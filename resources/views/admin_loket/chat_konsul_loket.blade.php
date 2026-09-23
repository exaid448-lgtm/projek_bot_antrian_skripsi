@extends('layout.navbar_admin')

@section('content')
<!-- Load Google Font & FontAwesome & Custom CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/chat_loket.css') }}?v={{ time() }}">

<!-- Success & Error Toast Notification -->
@if(session('success'))
    <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 12px 20px; border-radius: 8px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);" id="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
        <button type="button" onclick="document.getElementById('alert-success').remove()" style="background: none; border: none; color: inherit; cursor: pointer; margin-left: 10px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif
@if(session('error'))
    <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 12px 20px; border-radius: 8px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);" id="alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
        <button type="button" onclick="document.getElementById('alert-error').remove()" style="background: none; border: none; color: inherit; cursor: pointer; margin-left: 10px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

<div class="main-content">
    <div class="chat-container">
        
        <!-- SIDEBAR KIRI: Daftar Pengunjung -->
        <aside class="chat-sidebar">
            <div class="sidebar-header">
                <h3>Konsultasi Online <span class="badge" id="online-count">{{ isset($daftar_pengunjung) ? $daftar_pengunjung->count() : 0 }} Aktif</span></h3>
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="visitor-search" class="search-input" placeholder="Cari nama pengunjung...">
                </div>
            </div>
            
            <div class="visitor-list" id="visitor-list-container">
                @if(isset($daftar_pengunjung) && count($daftar_pengunjung) > 0)
                    @foreach($daftar_pengunjung as $item)
                        @php
                            $isAktif = isset($pengunjung_aktif) && $pengunjung_aktif->id_pengunjung == $item->id_pengunjung;
                            $inisial = strtoupper(substr($item->nama ?? 'Pengunjung', 0, 2));
                        @endphp
                        <a href="{{ route('chatloket.index', ['id_pengunjung' => $item->id_pengunjung]) }}" class="visitor-item {{ $isAktif ? 'active' : '' }}" data-name="{{ strtolower($item->nama) }}">
                            <div class="chat-avatar">
                                {{ $inisial }}
                                @if($item->unread_count > 0)
                                    <span class="avatar-online-dot"></span>
                                @endif
                            </div>
                            <div class="visitor-info">
                                <div class="visitor-info-top">
                                    <h4 class="visitor-name" data-full-name="{{ $item->nama }}">{{ $item->nama }}</h4>
                                    <span class="visitor-time">{{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('H:i') : '' }}</span>
                                </div>
                                <p class="visitor-message-preview">
                                    {{ $item->pesanTerakhir ? Str::limit($item->pesanTerakhir, 32) : 'Klik untuk mulai chat...' }}
                                </p>
                            </div>
                            @if(isset($item->unread_count) && $item->unread_count > 0)
                                <span class="unread-badge">{{ $item->unread_count }}</span>
                            @endif
                        </a>
                    @endforeach
                @else
                    <div class="empty-chat-state" style="padding: 40px 20px; text-align: center;">
                        <div class="empty-state-icon" style="font-size: 30px; color: var(--text-muted); margin-bottom: 15px;">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <h4 style="font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 5px;">Tidak Ada Chat</h4>
                        <p style="font-size: 11px; color: var(--text-muted); margin: 0;">Belum ada pengunjung yang berkonsultasi.</p>
                    </div>
                @endif
            </div>

            <!-- Footer: Profil Karyawan Login -->
            <div class="sidebar-footer">
                <div class="employee-profile-card">
                    @php
                        $namaKaryawan = isset($karyawan) ? $karyawan->nama_user : (session('nama') ?? 'Petugas Loket');
                        $fotoKaryawan = isset($karyawan) ? $karyawan->img_user : session('foto');
                        $inisialKaryawan = strtoupper(substr($namaKaryawan, 0, 2));
                    @endphp
                    
                    @if($fotoKaryawan && file_exists(public_path('img/foto_karyawan/' . $fotoKaryawan)))
                        <img src="{{ asset('img/foto_karyawan/' . $fotoKaryawan) }}" class="employee-avatar" alt="Foto Profil">
                    @elseif($fotoKaryawan && file_exists(public_path('storage/' . $fotoKaryawan)))
                        <img src="{{ asset('storage/' . $fotoKaryawan) }}" class="employee-avatar" alt="Foto Profil">
                    @else
                        <div class="employee-avatar-initials">
                            {{ $inisialKaryawan }}
                        </div>
                    @endif

                    <div class="employee-details">
                        <p class="employee-name">{{ $namaKaryawan }}</p>
                        <p class="employee-status">
                            <span class="status-dot"></span> Online
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- AREA UTAMA: Ruang Obrolan -->
        <main class="chat-area">
            @if(isset($pengunjung_aktif))
                <div class="chat-area-header">
                    <div class="header-user-info">
                        <div class="chat-avatar">
                            {{ strtoupper(substr($pengunjung_aktif->nama ?? 'P', 0, 2)) }}
                            <span class="avatar-online-dot"></span>
                        </div>
                        <div class="header-user-name">
                            <h4>{{ $pengunjung_aktif->nama }}</h4>
                            <div class="header-user-status">
                                <span class="status-dot"></span> Online · {{ $pengunjung_aktif->email ?? '-' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="header-actions">
                        <button type="button" class="btn-header-info" onclick="showVisitorInfo('{{ $pengunjung_aktif->nama }}', '{{ $pengunjung_aktif->email }}', '{{ $pengunjung_aktif->nomor_whatsapp }}')">
                            <i class="fa-solid fa-circle-info"></i> Info Detail
                        </button>
                    </div>
                </div>

                <div class="chat-messages-container" id="chat-messages-scroll">
                    @if(isset($riwayat_chat) && count($riwayat_chat) > 0)
                        @foreach($riwayat_chat as $pesan)
                            @php
                                $isOutgoing = $pesan->tipe_pengirim === 'karyawan';
                            @endphp
                            <div class="message-row {{ $isOutgoing ? 'outgoing' : 'incoming' }}">
                                @if(!$isOutgoing)
                                    <div class="message-avatar-container">
                                        <div class="msg-visitor-avatar">
                                            {{ strtoupper(substr($pengunjung_aktif->nama ?? 'P', 0, 2)) }}
                                        </div>
                                    </div>
                                @endif
                                <div class="message-bubble-wrapper">
                                    @if($isOutgoing)
                                        <div class="message-dropdown">
                                            <button class="message-actions-trigger" onclick="toggleMessageDropdown('msg-dropdown-{{ $pesan->id_chat }}', event)">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <div id="msg-dropdown-{{ $pesan->id_chat }}" class="message-dropdown-menu">
                                                <button type="button" class="message-dropdown-item" onclick="openEditMessageModal('{{ $pesan->id_chat }}', '{{ addslashes($pesan->pesan) }}')">
                                                    <i class="fa-solid fa-pen text-slate-400"></i> Edit
                                                </button>
                                                <button type="button" class="message-dropdown-item delete" onclick="openDeleteMessageModal('{{ $pesan->id_chat }}')">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="message-bubble">
                                        @if($pesan->file_lampiran)
                                            @php
                                                $ext = strtolower(pathinfo($pesan->file_lampiran, PATHINFO_EXTENSION));
                                                $fileUrl = Storage::url($pesan->file_lampiran);
                                            @endphp
                                            <div class="message-attachment">
                                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <a href="javascript:void(0)" onclick="openPreviewFile('{{ $fileUrl }}', 'image')">
                                                        <img src="{{ $fileUrl }}" alt="Lampiran" style="cursor: zoom-in;">
                                                    </a>
                                                @else
                                                    <div class="file-attachment-card">
                                                        <div class="file-icon" style="background-color: {{ $ext == 'pdf' ? '#ef4444' : '#3b82f6' }}; cursor: pointer;" onclick="openPreviewFile('{{ $fileUrl }}', '{{ $ext == 'pdf' ? 'pdf' : 'other' }}')">
                                                            <i class="fa-solid {{ $ext == 'pdf' ? 'fa-file-pdf' : 'fa-file-word' }}"></i>
                                                        </div>
                                                        <div class="file-info" style="cursor: pointer;" onclick="openPreviewFile('{{ $fileUrl }}', '{{ $ext == 'pdf' ? 'pdf' : 'other' }}')">
                                                            <p style="margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;">{{ basename($pesan->file_lampiran) }}</p>
                                                            <span style="font-size: 10px; opacity: 0.8;">{{ $pesan->ukuran_file ?? 'Unknown' }}</span>
                                                        </div>
                                                        <a href="{{ $fileUrl }}" target="_blank" class="file-download-btn" style="color: inherit; opacity: 0.8;" title="Unduh Berkas">
                                                            <i class="fa-solid fa-arrow-down-to-bracket"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if($pesan->pesan)
                                            <p style="margin:0;">{{ $pesan->pesan }}</p>
                                        @endif
                                        
                                        <div class="message-meta">
                                            <span>{{ $pesan->created_at ? \Carbon\Carbon::parse($pesan->created_at)->format('H:i') : '' }}</span>
                                            @if($isOutgoing)
                                                <i class="fa-solid fa-check-double read-tick" style="color: {{ $pesan->status_baca ? '#38bdf8' : 'rgba(255,255,255,0.4)' }};"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-chat-state">
                            <div class="empty-state-icon">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <h3>Awal Percakapan</h3>
                            <p>Ketik pesan Anda di bawah untuk memulai obrolan dengan pengunjung ini.</p>
                        </div>
                    @endif
                </div>

                <div class="chat-input-bar">
                    <!-- File Upload Preview -->
                    <div class="file-preview-bar" id="input-file-preview" style="display:none;">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span id="preview-file-name">file_lampiran.pdf</span>
                        <button type="button" class="btn-clear-preview" onclick="clearFileSelection()">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form action="{{ route('chatloket.kirim', ['id_pengunjung' => $pengunjung_aktif->id_pengunjung]) }}" method="POST" enctype="multipart/form-data" class="chat-input-form" id="real-chat-form">
                        @csrf
                        <label class="attachment-trigger" title="Unggah Berkas">
                            <input type="file" name="file_upload" id="real-file-input" style="display:none;" onchange="previewFile(this)">
                            <i class="fa-solid fa-paperclip"></i>
                        </label>
                        <div class="input-field-wrapper">
                            <input type="text" name="pesan" class="chat-text-input" placeholder="Tulis jawaban Anda..." autocomplete="off">
                        </div>
                        <button type="submit" class="chat-send-btn">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="empty-chat-state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; color: var(--text-muted); background: var(--bg-slate); padding: 40px;">
                    <div style="font-size: 60px; color: #cbd5e1; margin-bottom: 20px;">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 600; color: var(--text-dark); margin-bottom: 8px;">Pilih Pengunjung</h3>
                    <p style="font-size: 14px; max-width: 300px; margin: 0 auto; color: var(--text-muted);">Pilih salah satu pengunjung di sebelah kiri untuk mulai membaca dan membalas pesan konsultasi.</p>
                </div>
            @endif
        </main>
        
    </div>
</div>

<!-- ================= MODAL DETAIL INFORMASI PENGUNJUNG ================= -->
<div class="chat-modal-overlay" id="visitor-info-modal">
    <div class="chat-modal-box">
        <div class="chat-modal-header">
            <h4>Informasi Pengunjung</h4>
            <button class="btn-close-modal" onclick="closeVisitorInfo()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="chat-modal-body">
            <div style="text-align: center; margin-bottom: 20px;">
                <div id="modal-avatar" class="chat-avatar" style="width: 64px; height: 64px; font-size: 20px; border-radius: 18px; margin: 0 auto 12px auto; background: linear-gradient(135deg, #f472b6 0%, #db2777 100%);">
                    AP
                </div>
                <h4 id="modal-name" style="margin: 0; font-size: 16px; font-weight: 700; color: var(--text-dark);">Adelia Putri</h4>
                <span style="font-size: 12px; color: #10b981; font-weight: 600;"><i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 4px;"></i>Online</span>
            </div>
            
            <div style="border-top: 1px solid var(--border-color); padding-top: 15px;">
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Email</span>
                        <strong id="modal-email" style="color: var(--text-dark);">adelia.putri@email.com</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">No. WhatsApp</span>
                        <strong id="modal-phone" style="color: var(--text-dark);">0812-3456-7890</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Terdaftar Pada</span>
                        <strong style="color: var(--text-dark);">10 Juni 2026</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="chat-modal-footer">
            <button class="btn-chat-modal btn-cancel" onclick="closeVisitorInfo()">Tutup</button>
            <a id="whatsapp-link" href="https://wa.me/6281234567890" target="_blank" class="btn-chat-modal btn-primary" style="text-decoration:none; display: inline-flex; align-items:center; gap: 6px;">
                <i class="fa-brands fa-whatsapp"></i> Hubungi WA
            </a>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT PESAN ================= -->
<div class="chat-modal-overlay" id="edit-message-modal">
    <div class="chat-modal-box">
        <form id="edit-message-form" method="POST">
            @csrf
            <div class="chat-modal-header">
                <h4>Edit Pesan</h4>
                <button type="button" class="btn-close-modal" onclick="closeEditMessageModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="chat-modal-body">
                <textarea name="pesan" id="edit-message-textarea" class="chat-text-input" style="width: 100%; min-height: 100px; padding: 12px; border-radius: 12px; border: 1px solid var(--border-color); resize: vertical; box-sizing: border-box; font-family: inherit;" required></textarea>
            </div>
            <div class="chat-modal-footer">
                <button type="button" class="btn-chat-modal btn-cancel" onclick="closeEditMessageModal()">Batal</button>
                <button type="submit" class="btn-chat-modal btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL HAPUS PESAN ================= -->
<div class="chat-modal-overlay" id="delete-message-modal">
    <div class="chat-modal-box">
        <form id="delete-message-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="chat-modal-header">
                <h4>Hapus Pesan</h4>
                <button type="button" class="btn-close-modal" onclick="closeDeleteMessageModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="chat-modal-body">
                <p style="margin: 0; font-size: 13.5px; color: var(--text-dark); text-align: left; line-height: 1.5;">Apakah Anda yakin ingin menghapus pesan ini? Berkas lampiran yang terkait juga akan dihapus secara permanen.</p>
            </div>
            <div class="chat-modal-footer">
                <button type="button" class="btn-chat-modal btn-cancel" onclick="closeDeleteMessageModal()">Batal</button>
                <button type="submit" class="btn-chat-modal btn-primary" style="background: #ef4444; border-color: #ef4444;">Hapus</button>
            </div>
        </form>
    </div>

<!-- Scripts section -->
@push('scripts')
<script>
    // Scroll container helper
    function scrollChatToBottom() {
        const container = document.getElementById('chat-messages-scroll');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Real File Previews
    function previewFile(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            document.getElementById('preview-file-name').innerHTML = fileName;
            document.getElementById('input-file-preview').style.display = 'flex';
        }
    }

    function clearFileSelection() {
        const input = document.getElementById('real-file-input');
        if (input) input.value = '';
        document.getElementById('input-file-preview').style.display = 'none';
    }

    // Modal Operations
    function showVisitorInfo(name, email, phone) {
        document.getElementById('modal-name').innerHTML = name;
        document.getElementById('modal-email').innerHTML = email || '-';
        document.getElementById('modal-phone').innerHTML = phone || '-';
        
        const inisial = name.substring(0, 2).toUpperCase();
        const avatar = document.getElementById('modal-avatar');
        avatar.innerHTML = inisial;

        const waLink = document.getElementById('whatsapp-link');
        if (phone && phone !== '-') {
            const formattedPhone = phone.replace(/[^0-9]/g, '');
            // Convert leading 0 to 62
            let cleanPhone = formattedPhone;
            if (formattedPhone.startsWith('0')) {
                cleanPhone = '62' + formattedPhone.substring(1);
            }
            waLink.href = `https://wa.me/${cleanPhone}`;
            waLink.style.display = 'inline-flex';
        } else {
            waLink.style.display = 'none';
        }

        document.getElementById('visitor-info-modal').classList.add('active');
    }

    function closeVisitorInfo() {
        document.getElementById('visitor-info-modal').classList.remove('active');
    }

    // Dropdown Actions Toggles
    function toggleMessageDropdown(id, event) {
        event.stopPropagation();
        
        // Tutup dropdown lain
        const dropdowns = document.querySelectorAll('.message-dropdown-menu');
        dropdowns.forEach(d => {
            if (d.id !== id) d.classList.remove('show');
        });
        
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    // Edit Modal handlers
    function openEditMessageModal(id, pesanText) {
        const modal = document.getElementById('edit-message-modal');
        const form = document.getElementById('edit-message-form');
        const textarea = document.getElementById('edit-message-textarea');
        if (modal && form && textarea) {
            form.action = `/chat_loket/edit/${id}`;
            textarea.value = pesanText;
            modal.classList.add('active');
        }
    }

    function closeEditMessageModal() {
        const modal = document.getElementById('edit-message-modal');
        if (modal) {
            modal.classList.remove('active');
        }
    }

    // File Preview in New Tab with Close Button
    function openPreviewFile(fileUrl, fileType) {
        if (fileType === 'image' || fileType === 'pdf') {
            const previewWindow = window.open('', '_blank');
            if (previewWindow) {
                let contentHtml = '';
                if (fileType === 'image') {
                    contentHtml = '<img src="' + fileUrl + '" style="max-width: 90%; max-height: 80vh; object-fit: contain; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.55); margin-bottom: 25px;">';
                } else {
                    contentHtml = '<iframe src="' + fileUrl + '" style="width: 90%; height: 75vh; border-radius: 12px; border: none; background: white; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.55); margin-bottom: 25px;" frameborder="0"></iframe>';
                }
                
                previewWindow.document.write(`
                    <!DOCTYPE html>
                    <html lang="id">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Pratinjau Lampiran</title>
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
                        <style>
                            body {
                                margin: 0;
                                padding: 20px;
                                background-color: #0f172a;
                                font-family: 'Outfit', sans-serif;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                min-height: 100vh;
                                color: #ffffff;
                                overflow: hidden;
                                box-sizing: border-box;
                            }
                            .btn-close {
                                padding: 12px 28px;
                                background-color: #ef4444;
                                color: white;
                                border: none;
                                border-radius: 12px;
                                font-size: 14px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.2s ease;
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                                box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
                            }
                            .btn-close:hover {
                                background-color: #dc2626;
                                transform: translateY(-1px);
                            }
                            .btn-close:active {
                                transform: scale(0.95);
                            }
                        </style>
                    </head>
                    <body>
                ` + contentHtml + `
                        <div>
                            <button onclick="window.close()" class="btn-close">
                                <i class="fa-solid fa-xmark"></i> Tutup Halaman
                            </button>
                        </div>
                    </body>
                    </html>
                `);
                previewWindow.document.close();
                return;
            }
        }
        // Fallback jika window.open diblokir atau tipe file lain
        window.open(fileUrl, '_blank');
    }

    // Delete Modal handlers
    function openDeleteMessageModal(id) {
        const modal = document.getElementById('delete-message-modal');
        const form = document.getElementById('delete-message-form');
        if (modal && form) {
            form.action = `/chat_loket/hapus/${id}`;
            modal.classList.add('active');
        }
    }

    function closeDeleteMessageModal() {
        const modal = document.getElementById('delete-message-modal');
        if (modal) {
            modal.classList.remove('active');
        }
    }

    // Close Dropdowns and Modals on click outside
    document.addEventListener('click', function(e) {
        // Close menus
        const dropdowns = document.querySelectorAll('.message-dropdown-menu');
        dropdowns.forEach(d => d.classList.remove('show'));

        // Close visitor details modal
        const visitorModal = document.getElementById('visitor-info-modal');
        if (visitorModal && e.target === visitorModal) {
            closeVisitorInfo();
        }

        // Close edit modal
        const editModal = document.getElementById('edit-message-modal');
        if (editModal && e.target === editModal) {
            closeEditMessageModal();
        }

        // Close delete modal
        const deleteModal = document.getElementById('delete-message-modal');
        if (deleteModal && e.target === deleteModal) {
            closeDeleteMessageModal();
        }
    });

    // Close modal on Escape key press
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVisitorInfo();
            closeEditMessageModal();
            closeDeleteMessageModal();
        }
    });

    // Search filter logic
    document.getElementById('visitor-search').addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.visitor-item').forEach(item => {
            const nameEl = item.querySelector('.visitor-name');
            const name = nameEl.getAttribute('data-full-name') || nameEl.innerText;
            if (name.toLowerCase().includes(keyword)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Auto scroll and init alert fade outs
    window.addEventListener('DOMContentLoaded', () => {
        scrollChatToBottom();

        // Alert timeouts
        setTimeout(() => {
            const successAlert = document.getElementById('alert-success');
            if (successAlert) successAlert.remove();
            const errorAlert = document.getElementById('alert-error');
            if (errorAlert) errorAlert.remove();
        }, 4000);
    });
</script>
@endpush
@endsection