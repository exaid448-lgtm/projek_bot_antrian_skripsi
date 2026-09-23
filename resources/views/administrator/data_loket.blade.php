<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Loket - Super Admin</title>
    <link rel="stylesheet" href="{{ asset('css/data_loket.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    @extends('layout.navbar_administrator')

    @section('content')
    <div class="main-content">
        <div class="page-header">
            <h2 class="page-title">Manajemen Data Loket</h2>
            <div class="header-tools">
                <div class="search-box-custom">
                    <input type="text" id="searchInput" placeholder="Cari data loket...">
                    <button id="btnSearch" class="btn-search-blue">Search</button>
                </div>
                <button class="btn-add" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Tambah Loket
                </button>
            </div>
        </div>

        <div class="table-card-container">
            <div class="card card-table">
                <h4 class="card-inner-title">Tabel Daftar Loket</h4>
                <div class="table-container">
                    <table class="table-basic">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Loket</th>
                                <th>Nama Pelayanan</th>
                                <th>Lokasi Loket</th>
                                <th>Prefix</th>
                                <th>Logo</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data_loket as $key => $loket)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="font-bold">{{ $loket->nama_loket }}</td>
                                <td>{{ $loket->nama_pelayanan }}</td>
                                <td>{{ $loket->lokasi_loket }}</td>
                                <td><span class="badge-prefix">{{ $loket->prefix }}</span></td>
                                <td>
                                    @if($loket->logo)
                                        {{-- Kita cek jika di DB sudah ada kata 'logo_loket', jika belum kita tambahkan manual --}}
                                        @php
                                            $pathLogo = str_contains($loket->logo, 'logo_loket') 
                                                        ? 'img/' . $loket->logo 
                                                        : 'img/logo_loket/' . $loket->logo;
                                        @endphp
                                        <img src="{{ asset($pathLogo) }}" class="img-table-mini" alt="Logo">
                                    @else
                                        <img src="{{ asset('img/logo_placeholder.png') }}" class="img-table-mini" alt="No Logo">
                                    @endif
                                </td>
                                <td class="action-buttons">
                                    <button class="btn-edit" title="Edit" onclick="showEditModal({{ json_encode($loket) }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete" title="Hapus" onclick="showDeleteModal({{ $loket->id_loket }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            <tr id="noDataRow" style="display: none;">
                                    <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                                        Data loket tidak ditemukan...
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="modalLoket" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Data Loket</h3>
                <button class="close-btn" onclick="closeModal('modalLoket')">&times;</button>
            </div>
            <form id="formLoket" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Loket</label>
                        <input type="text" name="nama_loket" id="in_nama_loket" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Pelayanan</label>
                        <input type="text" name="nama_pelayanan" id="in_nama_pelayanan" required>
                    </div>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label>Lokasi Loket</label>
                            <input type="text" name="lokasi" id="in_lokasi" required>
                        </div>
                        <div class="form-group">
                            <label>Prefix</label>
                            <input type="text" name="prefix" id="in_prefix" maxlength="2" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Upload Logo (Kosongkan jika tidak diubah)</label>
                        <input type="file" name="logo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalLoket')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalDelete" class="modal-overlay">
        <div class="modal-content modal-small">
            <div class="modal-body text-center">
                <div class="delete-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <h3>Hapus Data Loket?</h3>
                <p>Data tidak bisa dikembalikan.</p>
            </div>
            <div class="modal-footer flex-center">
                <button class="btn-secondary" onclick="closeModal('modalDelete')">Batal</button>
                <form id="formDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Lanjut Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session("error") }}',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            @endif
        </script>
        <script src="{{ asset('js/data_loket.js') }}"></script>
    @endpush
</body>
</html>