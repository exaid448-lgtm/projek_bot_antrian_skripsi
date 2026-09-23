<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/data_algoritma.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
@extends('layout.navbar_admin')

@section('content')


<div class="main-content">
    <div class="glass-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4>Data Algoritma Loket: {{ $user->name ?? 'Admin' }}</h4>
            
            <div style="display: flex; gap: 10px;">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Cari algoritma...">
                    <button class="search-btn" id="btnSearch">Search</button>
                </div>
                <button class="btn-tambah" onclick="openModal('tambah')">
                    <i class="fas fa-plus"></i> Tambah Algoritma
                </button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>ALGORITMA</th>
                        <th>TANGGAL DAN WAKTU</th>
                        <th>TIPE LAYANAN</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody id="algoritmaTableBody">
                    @forelse($algoritmas as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td><span class="badge-algo">{{ $item->algoritma }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_dan_waktu)->format('d M Y, H:i') }}</td>
                        <td>{{ ucfirst($item->tipe_layanan) }}</td>
                        <td>
                            <button class="btn-edit" 
                                onclick="openModal('edit', '{{ $item->id_algoritma }}', '{{ $item->algoritma }}', '{{ $item->tanggal_dan_waktu }}', '{{ $item->tipe_layanan }}')">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="{{ route('algoritma.destroy', $item->id_algoritma) }}" 
                                method="POST" 
                                id="delete-form-{{ $item->id_algoritma }}" 
                                style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDelete('{{ $item->id_algoritma }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center; padding:20px;">Belum ada data antrian untuk loket ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalAlgoritma" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Tambah Algoritma</h3>
        <hr>
        <form id="formAlgoritma" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div class="form-group">
                <label>Algoritma (Nama Antrian)</label>
                <input type="text" name="algoritma" id="in_algoritma" class="form-control" placeholder="Masukan nama algoritma..." required>
            </div>
            <div class="form-group">
                <label>Tanggal dan Waktu</label>
                <input type="datetime-local" name="tanggal_dan_waktu" id="in_tanggal" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Tipe Layanan</label>
                <select name="tipe_layanan" id="in_tipe" class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="layanan">Layanan</option>
                    <option value="lokasi">Lokasi</option>
                </select>
            </div>
            <div style="text-align: right; margin-top: 25px;">
                <button type="button" class="btn-batal" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-simpan" id="btnSubmit">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/data_algoritma.js') }}"></script>
@endpush
</body>
</html>