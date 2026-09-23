<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Persyaratan Layanan</title>
    <link rel="stylesheet" href="{{ asset('css/persyaratan_loket.css') }}"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
@extends('layout.navbar_admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/persyaratan_loket.css') }}"> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="syarat-main-wrapper">
    
    <div class="syarat-header-flex">
        <div class="syarat-title-section page-title">
            <h2>📋 Persyaratan & Informasi Layanan</h2>
            <p>Kelola daftar dokumen penunjang, alur administrasi, serta tipe syarat khusus untuk <strong>{{ Auth::user()->loket->nama_loket ?? 'Loket Anda' }}</strong>.</p>
        </div>
        <div>
            <button type="button" class="skm-btn-gradient" id="btnBukaModalSyarat">
                <i class="fa fa-plus-circle"></i> Tambah Syarat Layanan
            </button>
        </div>
    </div>

    <div class="syarat-card">
        <div class="skm-table-responsive">
            <table class="skm-table-content">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="32%">Layanan / Nama Syarat</th>
                        <th width="12%">Tipe Syarat</th>
                        <th width="24%">Keterangan</th>
                        <th width="15%">Tanggal diuplode</th>
                        <th width="15%">Tanggal Diperbarui</th>
                        <th width="12%" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_syarat as $index => $syarat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="syarat-text-bold">{{ $syarat->nama_syarat }}</span></td>
                            <td>
                                @if($syarat->tipe_syarat == 'dokumen')
                                    <span class="badge-tipe-dokumen">Dokumen</span>
                                @else
                                    <span class="badge-tipe-prosedur" style="background-color: #ec4899; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Prosedur</span>
                                @endif
                            </td>
                            <td><span class="syarat-text-muted">{{ $syarat->keterangan }}</span></td>
                            <td><span class="syarat-text-muted">{{ \Carbon\Carbon::parse($syarat->created_at)->format('d-m-Y H:i') }}</span></td>
                            <td><span class="syarat-text-muted">{{ \Carbon\Carbon::parse($syarat->updated_at)->format('d-m-Y H:i') }}</span></td>
                            <td>
                                <div class="syarat-action-group">
                                    <button type="button" class="btn-circle-edit btn-edit-trigger" 
                                            data-id="{{ $syarat->id_syarat }}"
                                            data-nama="{{ $syarat->nama_syarat }}"
                                            data-tipe="{{ $syarat->tipe_syarat }}"
                                            data-keterangan="{{ $syarat->keterangan }}"
                                            title="Edit Data">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <button type="button" class="btn-circle-hapus btn-hapus-trigger" 
                                            data-id="{{ $syarat->id_syarat }}" 
                                            data-nama="{{ $syarat->nama_syarat }}" 
                                            title="Hapus Data">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #a0aec0; padding: 20px;">Belum ada data persyaratan untuk loket ini.</td>
                        </tr>      
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<input type="hidden" id="urlStoreRoute" value="{{ url('/admin-loket/persyaratan/store') }}">
<input type="hidden" id="urlUpdateRoute" value="{{ url('/admin-loket/persyaratan/update') }}">

<div id="modalPersyaratan" class="syarat-modal-overlay">
    <div class="syarat-modal-content">
        <div class="syarat-modal-header">
            <h3 id="modalTitleText">
                <i class="fa fa-folder-plus" id="modalTitleIcon"></i> Input Persyaratan Baru
            </h3>
            <button type="button" id="btnTutupX" class="syarat-modal-close-x">&times;</button>
        </div>
        <hr class="syarat-modal-divider">
        
        <form action="#" method="POST" id="formPersyaratan">
            @csrf
            <div id="methodSpoofingContainer"></div>
            
            <div class="syarat-form-group">
                <label>Loket Penginput</label>
                <input type="hidden" name="id_loket" value="{{ Auth::user()->id_loket ?? '' }}">
                <input type="text" class="input-readonly-custom" value="{{ Auth::user()->loket->nama_loket ?? 'BPJS Kesehatan (Otomatis)' }}" readonly>
                <small class="form-help-text">*Anda hanya dapat memanajemen data untuk loket tempat Anda bertugas.</small>
            </div>

            <div class="syarat-form-group">
                <label for="nama_syarat">Nama Layanan / Keperluan</label>
                <input type="text" id="nama_syarat" name="nama_syarat" placeholder="Contoh: Pembuatan Baru BPJS Kesehatan PBI" required>
            </div>

            <div class="syarat-form-group">
                <label for="tipe_syarat">Tipe Persyaratan</label>
                <select id="tipe_syarat" name="tipe_syarat" required>
                    <option value="dokumen">Dokumen (Berkas Fisik / Fotokopi)</option>
                    <option value="prosedur">Prosedur (Langkah-langkah / Alur)</option>
                </select>
            </div>

            <div class="syarat-form-group">
                <label for="keterangan">Keterangan Rincian Syarat & Biaya</label>
                <textarea id="keterangan" name="keterangan" rows="4" placeholder="Masukkan detail berkas yang diperlukan atau langkah-langkah alur..." required></textarea>
            </div>

            <div class="syarat-modal-footer">
                <button type="button" class="btn-modal-batal" id="btnBatal">Batal</button>
                <button type="submit" class="btn-modal-simpan" id="btnSubmitModal">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<form id="formHapusData" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
    });
</script>
@endif

<script src="{{ asset('js/persyratan_loket.js') }}"></script>

@endsection
</body>
</html>