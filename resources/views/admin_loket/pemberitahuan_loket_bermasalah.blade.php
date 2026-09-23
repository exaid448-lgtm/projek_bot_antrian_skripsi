<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/notif_masalah.css') }}">
</head>
<body>
@extends('layout.navbar_admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/notif_masalah.css') }}">

<div class="main-content">
    <div class="header-flex">
        <div>
            <h2 class="page-title">Pemberitahuan & Kendala Loket</h2>
            <p style="color: #64748b; margin: 5px 0 0 0; font-size: 14px;">
                Kelola pengumuman gangguan layanan untuk <strong>{{ $nama_loket }}</strong>
            </p>
        </div>
        <button class="btn-add-notification" onclick="openModal('modalTambah')">
            ➕ Tambah Pemberitahuan
        </button>
    </div>

    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- GRID KARTU NOTIFIKASI DARI DATABASE --}}
    <div class="notification-grid">
        
        @forelse ($informasi as $info)
        <div class="card-notification card-{{ $info->kategori_info }}">
            <div class="notification-meta">
                <div>
                    <span class="badge-category badge-{{ $info->kategori_info }}">{{ $info->kategori_info }}</span>
                    <span class="notification-date">• Diperbarui: {{ \Carbon\Carbon::parse($info->tanggal_info)->format('d M Y') }}</span>
                </div>
                <div class="status-indicator">
                    @if($info->status_info == 'aktif')
                        <span class="status-active">🟢 Aktif (Tampil di MPP)</span>
                    @else
                        <span class="status-archive">⚫ Diarsipkan</span>
                    @endif
                </div>
            </div>

            <h3 class="notification-title">{{ $info->judul_info }}</h3>

            <div class="info-box-wrapper">
                <div class="info-section">
                    <h5><span>⚠️</span> Detail Masalah:</h5>
                    <p>{{ $info->deskripsi_info }}</p>
                </div>
                <div class="info-section">
                    <h5><span>💡</span> Solusi / Alur Alternatif:</h5>
                    <p>{{ $info->solusi_info }}</p>
                </div>
            </div>

            <div class="card-actions">
                <button class="btn-action-icon" 
                        onclick="openModalEdit({{ json_encode($info) }})" 
                        title="Edit Data">✏️</button>
                
                <button type="button" class="btn-action-icon btn-delete" 
                        onclick="openModalHapus({{ $info->id_informasi }}, '{{ addslashes($info->judul_info) }}')" 
                        title="Hapus">❌</button>
            </div>
        </div>
        @empty
        <div class="card-notification" style="text-align: center; padding: 40px; color: #64748b;">
            <p style="font-size: 16px; font-weight: 600; margin: 0;">Sistem Normal Berjalan Baik 🚀</p>
            <p style="margin: 5px 0 0 0; font-size: 14px;">Tidak ada data kendala layanan yang aktif pada loket ini.</p>
        </div>
        @endforelse

    </div>
</div>

{{-- MODAL POPUP TAMBAH DATA --}}
<div id="modalTambah" class="custom-modal">
    <div class="modal-content-box">
        <div class="modal-header">
            <h3>Tambah Pemberitahuan Baru</h3>
            <span class="close-btn" onclick="closeModal('modalTambah')">&times;</span>
        </div>
        <form action="{{ route('notif_bermasalah.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Judul Informasi / Masalah</label>
                <input type="text" name="judul_info" placeholder="Contoh: Gangguan Jaringan Sistem Loket" required>
            </div>
            <div class="form-group-row">
                <div class="form-group">
                    <label>Kategori Tingkat Urgensi</label>
                    <select name="kategori_info" required>
                        <option value="normal">Normal (Hijau)</option>
                        <option value="warning">Warning (Kuning)</option>
                        <option value="critical">Critical (Merah)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Tampilan</label>
                    <select name="status_info" required>
                        <option value="aktif">Aktif (Tampilkan)</option>
                        <option value="arsip">Arsip (Sembunyikan)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Detail Deskripsi Masalah</label>
                <textarea name="deskripsi_info" rows="3" placeholder="Jelaskan secara rinci kendala teknis yang sedang terjadi..." required></textarea>
            </div>
            <div class="form-group">
                <label>Solusi / Alur Alternatif Pengunjung</label>
                <textarea name="solusi_info" rows="3" placeholder="Berikan arahan solusi atau kemana pengunjung harus mengurus berkasnya..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalTambah')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Pemberitahuan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL POPUP EDIT DATA --}}
<div id="modalEdit" class="custom-modal">
    <div class="modal-content-box">
        <div class="modal-header">
            <h3>Edit Pemberitahuan Kendala</h3>
            <span class="close-btn" onclick="closeModal('modalEdit')">&times;</span>
        </div>
        <form id="formEditJadwal" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Judul Informasi / Masalah</label>
                <input type="text" name="judul_info" id="edit_judul_info" required>
            </div>
            <div class="form-group-row">
                <div class="form-group">
                    <label>Kategori Tingkat Urgensi</label>
                    <select name="kategori_info" id="edit_kategori_info" required>
                        <option value="normal">Normal (Hijau)</option>
                        <option value="warning">Warning (Kuning)</option>
                        <option value="critical">Critical (Merah)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Tampilan</label>
                    <select name="status_info" id="edit_status_info" required>
                        <option value="aktif">Aktif (Tampilkan)</option>
                        <option value="arsip">Arsip (Sembunyikan)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Detail Deskripsi Masalah</label>
                <textarea name="deskripsi_info" id="edit_deskripsi_info" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Solusi / Alur Alternatif Pengunjung</label>
                <textarea name="solusi_info" id="edit_solusi_info" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL CUSTOM POPUP HAPUS (Sesuai Desain image_3ecddd.png) --}}
<div id="modalHapus" class="custom-modal">
    <div class="modal-content-box" style="max-width: 400px; text-align: center;">
        <div style="font-size: 50px; color: #ef4444; margin-bottom: 10px;">🗑️</div>
        <h3 style="margin-bottom: 10px; color: #1e293b;">Konfirmasi Hapus</h3>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
            Apakah Anda yakin ingin menghapus pemberitahuan <strong id="text_judul_hapus" style="color: #0f172a;"></strong> secara permanen?
        </p>
        
        <form id="formHapusData" action="" method="POST">
            @csrf
            @method('DELETE')
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="btn-secondary" onclick="closeModal('modalHapus')" style="flex: 1;">Batal</button>
                <button type="submit" class="btn-primary" style="background-color: #ef4444; flex: 1;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/notif_masalah.js') }}"></script>
@endsection
</body>
</html>