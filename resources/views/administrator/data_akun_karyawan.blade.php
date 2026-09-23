<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manajemen Akun - Super Admin</title>
    <link rel="stylesheet" href="{{ asset('css/data_akun.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Import SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    @extends('layout.navbar_administrator')
    
    @section('content')
    <div class="main-content">
        <div class="page-header">
            <h2 class="page-title">Manajemen Data Akun Karyawan</h2>
            <div class="header-tools">
                <div class="search-box-custom">
                    <input type="text" id="searchInput" placeholder="Cari nama atau email...">
                    <button id="btnSearch" class="btn-search-blue">Search</button>
                </div>
                <button class="btn-add" onclick="showUndangModal()">
                    <i class="fas fa-envelope"></i> Undang Karyawan
                </button>
            </div>
        </div>

        <div class="table-card-container">
            <div class="card card-table">
                <h4 class="card-inner-title">Tabel Daftar Akun Karyawan</h4>
                <div class="table-container">
                    <table class="table-basic">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Akun</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Loket</th>
                                <th>Jenis Kelamin</th>
                                <th>Foto</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="akunTableBody">
                            @php $no = 1; @endphp
                            @foreach($users as $user)
                                {{-- HANYA TAMPILKAN JIKA BUKAN SUPER ADMIN --}}
                                @if($user->kategori !== 'administrator')
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td class="font-bold">{{ $user->profil->nama_user ?? 'N/A' }}</td>
                                    <td>{{ optional($user->profil)->email ?? 'Email Kosong di DB' }}</td>
                                    <td><span class="badge-prefix">{{ $user->username }}</span></td>
                                    <td>{{ $user->profil->loket->nama_loket ?? '-' }}</td>
                                    <td>{{ $user->profil->jenis_kelamin ?? '-' }}</td>
                                    <td>
                                        <img src="{{ ($user->profil && $user->profil->img_user && file_exists(public_path('img/foto_karyawan/'.$user->profil->img_user))) 
                                            ? asset('img/foto_karyawan/'.$user->profil->img_user) 
                                            : asset('img/user_default.png') }}" 
                                            class="img-table-mini">                                
                                    </td>
                                    <td class="action-buttons">
                                        @php
                                            $editData = [
                                                'id'       => $user->id_user,
                                                'name'     => $user->profil->nama_user ?? '',
                                                'email'    => $user->profil->email ?? '',
                                                'username' => $user->username,
                                                'jk'       => $user->profil->jenis_kelamin ?? '',
                                                'tgl'      => $user->profil->tanggal_lahir ?? '',
                                                'loket'    => $user->profil->id_loket ?? ''
                                            ];
                                        @endphp

                                        <button class="btn-edit"
                                            title="Edit Akun"
                                            onclick='showEditModal(@json($editData))'>
                                            <i class="fas fa-user-cog"></i>
                                        </button>

                                        <button class="btn-delete" title="Hapus Akun" onclick="showDeleteModal({{ $user->id_user }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FORM UNDANG --}}
    <div id="modalUndang" class="modal-overlay">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3 id="modalUndangTitle">Kirim Undangan Registrasi</h3>
                <button class="close-btn" onclick="closeModal('modalUndang')">&times;</button>
            </div>
            <form id="formUndang" action="{{ route('data_akun.undang') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">Tautan registrasi khusus akan dikirim ke email ini.</p>
                    <div class="form-group">
                        <label>Email Karyawan</label>
                        <input type="email" name="email" id="undang_email" required>
                    </div>
                    <div class="form-section-title">Penempatan Loket</div>
                    <div class="form-group">
                        <label>Pilih Loket Induk</label>
                        <select id="undang_sel_loket" class="form-select-custom" required>
                            <option value="">-- Pilih Loket --</option>
                            @foreach($data_loket->unique('nama_loket') as $lkt)
                                <option value="{{ $lkt->nama_loket }}">{{ $lkt->nama_loket }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Layanan Spesifik</label>
                        <select name="id_loket" id="undang_sel_layanan" class="form-select-custom" disabled required>
                            <option value="">-- Pilih Layanan --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalUndang')">Batal</button>
                    <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> Kirim</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL FORM EDIT --}}
    <div id="modalAkun" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Edit Data Karyawan</h3>
                <button class="close-btn" onclick="closeModal('modalAkun')">&times;</button>
            </div>
            <form id="formAkun" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="form-section-title">Informasi Pribadi</div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" id="in_name" required>
                    </div>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="in_jenis_kelamin" class="form-select-custom" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="in_tanggal_lahir" required>
                        </div>
                    </div>
                    <div class="form-section-title">Penempatan Loket</div>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label>Pilih Loket</label>
                            <select id="sel_loket" class="form-select-custom">
                                <option value="">-- Pilih Loket --</option>
                                @foreach($data_loket->unique('nama_loket') as $lkt)
                                    <option value="{{ $lkt->nama_loket }}">{{ $lkt->nama_loket }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Layanan Loket</label>
                            <select name="id_loket" id="sel_layanan" class="form-select-custom" disabled required>
                                <option value="">-- Pilih Layanan --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-section-title">Kredensial Login</div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="in_email" required>
                    </div>
                    <div class="mt-4 p-4 bg-red-50 rounded-lg border border-red-100 flex items-center justify-between" style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 15px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <div>
                            <strong style="color: #991b1b; display: block; font-size: 14px;">Lupa Password?</strong>
                            <span style="color: #b91c1c; font-size: 12px;">Kirimkan tautan reset kredensial ke email karyawan.</span>
                        </div>
                        <button type="button" class="btn-danger" id="btnResetPassword" onclick="kirimResetAkun()" style="padding: 8px 15px; font-size: 13px;">
                            <i class="fas fa-key"></i> Kirim Reset
                        </button>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Foto Karyawan</label>
                        <input type="file" name="foto" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAkun')">Batal</button>
                    <button type="submit" class="btn-primary" id="btnSubmitForm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div id="modalDelete" class="modal-overlay">
        <div class="modal-content modal-small">
            <div class="modal-body text-center">
                <h3>Hapus Akun?</h3>
                <p>Data ini tidak bisa dikembalikan.</p>
            </div>
            <div class="modal-footer flex-center">
                <button class="btn-secondary" onclick="closeModal('modalDelete')">Batal</button>
                <form id="formDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>window.allLoketData = @json($data_loket);</script>

    {{-- Notifikasi SweetAlert2 Popup --}}
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3b82f6',
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444',
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                html: '<ul style="text-align: left; list-style-position: inside; font-family: sans-serif; font-size: 14px; color: #475569;">' +
                      '@foreach($errors->all() as $error)' +
                      '<li style="margin-bottom: 5px;">{{ $error }}</li>' +
                      '@endforeach' +
                      '</ul>',
                confirmButtonColor: '#ef4444',
            });
        </script>
    @endif
    @endsection

    @push('scripts')
        <script src="{{ asset('js/data_akun.js') }}"></script>
    @endpush

</body>
</html>