@extends('layout.navbar_administrator')
@section('title', 'Validasi Prioritas Pengunjung')

@section('content')
<div class="main-content">
    <div class="page-header">
        <div>
            <h2 class="page-title" style="margin-bottom: 0;">Validasi Hak Prioritas</h2>
            <p style="color: #6c757d; margin-top: 5px; font-size: 14px;">Kelola persetujuan prioritas pengunjung dari profil mereka (Lansia otomatis, tidak perlu validasi).</p>
        </div>
    </div>

    @if(session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Section 1: Pengajuan Prioritas Menunggu Validasi -->
    <div class="table-card-container" style="margin-bottom: 30px;">
        <div class="card card-table">
            <h4 class="card-inner-title">Pengajuan Prioritas Menunggu Validasi</h4>
            <div class="table-container">
                <table class="table-basic">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengunjung</th>
                            <th>Jenis Prioritas</th>
                            <th>Dokumen</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profilMenunggu as $key => $p)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td style="font-weight: bold;">{{ $p->nama }}</td>
                            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $p->jenis_prioritas) }}</td>
                            <td>
                                @if($p->dokumen_prioritas)
                                    <button type="button" onclick="showViewDokumen('{{ $p->dokumen_prioritas }}', '{{ asset('storage/'.$p->dokumen_prioritas) }}')" style="background: none; border: none; color: #0056b3; text-decoration: underline; cursor: pointer; padding: 0;">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat Dokumen
                                    </button>
                                @else
                                    <span style="color: #999; font-style: italic;">Tidak ada dokumen</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button onclick="confirmApprove('{{ $p->id_pengunjung }}', '{{ $p->jenis_prioritas }}')" style="background: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Setujui</button>
                                <button onclick="showTolakModal('{{ $p->id_pengunjung }}')" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Tolak</button>

                                <form id="form-approve-{{ $p->id_pengunjung }}" action="{{ route('admin.validasi_profil.setuju', $p->id_pengunjung) }}" method="POST" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="tanggal_berakhir_prioritas" id="input-date-{{ $p->id_pengunjung }}">
                                </form>

                                <!-- Simple modal HTML structure, hidden by default -->
                                <div id="modalTolakProfil-{{ $p->id_pengunjung }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
                                    <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%; text-align: left;">
                                        <h3 style="margin-top: 0;">Tolak Pengajuan Prioritas</h3>
                                        <form action="{{ route('admin.validasi_profil.tolak', $p->id_pengunjung) }}" method="POST">
                                            @csrf
                                            <div style="margin-bottom: 15px;">
                                                <label style="display: block; margin-bottom: 5px;">Alasan Penolakan</label>
                                                <textarea name="alasan_penolakan" rows="3" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"></textarea>
                                            </div>
                                            <div style="text-align: right;">
                                                <button type="button" onclick="hideTolakModal('{{ $p->id_pengunjung }}')" style="background: #f8f9fa; border: 1px solid #ddd; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Batal</button>
                                                <button type="submit" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Tolak Pengajuan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999; font-style: italic;">Tidak ada pengajuan prioritas dari profil.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 2: Riwayat Validasi Prioritas -->
    <div class="table-card-container">
        <div class="card card-table">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 class="card-inner-title" style="margin: 0;">Riwayat Validasi Prioritas</h4>
                <button type="button" onclick="confirmCetak()" class="btn-add" style="background-color: #0d6efd; color: white;">
                    <i class="fa-solid fa-print"></i> Cetak Laporan
                </button>
            </div>
            
            <form action="{{ route('admin.validasi_prioritas') }}" method="GET" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #eee;">
                <div style="margin-bottom: 10px; font-weight: bold; color: #555;">
                    <i class="fa-solid fa-filter"></i> Filter Data Riwayat
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px;">Cari Pengunjung</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama..." style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px;">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px;">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" style="background: #343a40; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Terapkan</button>
                        <a href="{{ route('admin.validasi_prioritas') }}" style="background: white; border: 1px solid #ccc; color: #333; padding: 8px 15px; border-radius: 4px; text-decoration: none;">Reset</a>
                    </div>
                </div>
            </form>
            
            <div class="table-container">
                <table class="table-basic">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengunjung</th>
                            <th>Jenis Prioritas</th>
                            <th>Status</th>
                            <th>Batas Waktu</th>
                            <th>Catatan</th>
                            <th>Dokumen</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profilRiwayat as $key => $r)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td style="font-weight: bold;">{{ $r->nama }}</td>
                            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $r->jenis_prioritas) }}</td>
                            <td>
                                @if($r->status_prioritas == 'disetujui')
                                    <span style="background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">Disetujui</span>
                                @elseif($r->status_prioritas == 'ditolak')
                                    <span style="background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                {{ $r->tanggal_berakhir_prioritas ? \Carbon\Carbon::parse($r->tanggal_berakhir_prioritas)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                @if($r->status_prioritas == 'ditolak' && $r->alasan_penolakan_prioritas)
                                    <span title="{{ $r->alasan_penolakan_prioritas }}">{{ Str::limit($r->alasan_penolakan_prioritas, 30) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($r->dokumen_prioritas)
                                    <button type="button" onclick="showViewDokumen('{{ $r->dokumen_prioritas }}', '{{ asset('storage/'.$r->dokumen_prioritas) }}')" style="background: none; border: none; color: #0d6efd; text-decoration: underline; font-size: 12px; font-weight: bold; cursor: pointer; padding: 0;">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat Dokumen
                                    </button>
                                @else
                                    <span style="color: #999; font-style: italic; font-size: 12px;">Tidak ada</span>
                                @endif
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <button onclick="showEditModal('{{ $r->id_pengunjung }}', '{{ $r->status_prioritas }}', '{{ $r->tanggal_berakhir_prioritas }}', '{{ htmlspecialchars($r->alasan_penolakan_prioritas, ENT_QUOTES) }}')" style="background: #ffc107; color: #212529; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-right: 5px;" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.validasi_profil.hapus', $r->id_pengunjung) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat prioritas pengunjung ini?');">
                                    @csrf
                                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #999; font-style: italic;">Belum ada riwayat validasi prioritas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Dokumen -->
<div id="modalViewDokumen" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; padding: 15px; border-radius: 8px; width: 80%; height: 90%; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 style="margin: 0;">Lihat Dokumen</h3>
            <button onclick="hideViewDokumen()" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Tutup</button>
        </div>
        <iframe id="iframeDokumen" src="" style="width: 100%; flex-grow: 1; border: 1px solid #ddd; border-radius: 4px;"></iframe>
    </div>
</div>

<!-- Modal Edit Riwayat -->
<div id="modalEditProfil" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%; text-align: left;">
        <h3 style="margin-top: 0;">Edit Validasi Prioritas</h3>
        <form id="formEditProfil" action="" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Status Prioritas</label>
                <select name="status_prioritas" id="edit-status" onchange="toggleEditFields()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;" required>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
            <div id="edit-tanggal-container" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Batas Waktu (Jika Disetujui)</label>
                <input type="date" name="tanggal_berakhir_prioritas" id="edit-tanggal" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div id="edit-alasan-container" style="margin-bottom: 15px; display: none;">
                <label style="display: block; margin-bottom: 5px;">Alasan Penolakan (Jika Ditolak)</label>
                <textarea name="alasan_penolakan" id="edit-alasan" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"></textarea>
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="hideEditModal()" style="background: #f8f9fa; border: 1px solid #ddd; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Batal</button>
                <button type="submit" style="background: #0d6efd; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showTolakModal(id) {
        let modal = document.getElementById('modalTolakProfil-' + id);
        if(modal) {
            modal.style.display = 'flex';
        }
    }
    
    function hideTolakModal(id) {
        let modal = document.getElementById('modalTolakProfil-' + id);
        if(modal) {
            modal.style.display = 'none';
        }
    }

    function showViewDokumen(path, fallbackUrl) {
        document.getElementById('modalViewDokumen').style.display = 'flex';
        let iframe = document.getElementById('iframeDokumen');
        iframe.src = ''; // reset

        // Menggunakan POST request ke backend untuk mendapatkan base64
        // IDM TIDAK PERNAH mencegat request POST atau respons JSON
        fetch('{{ route("admin.validasi_dokumen.base64") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ path: path })
        })
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data) {
                const byteCharacters = atob(res.data);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], {type: res.mime});
                const blobUrl = URL.createObjectURL(blob);
                iframe.src = blobUrl;
            } else {
                console.error("Gagal mendapatkan dokumen:", res.message);
                iframe.src = fallbackUrl; // fallback
            }
        })
        .catch(error => {
            console.error("Error mengambil file:", error);
            iframe.src = fallbackUrl; // fallback
        });
    }

    function hideViewDokumen() {
        document.getElementById('iframeDokumen').src = '';
        document.getElementById('modalViewDokumen').style.display = 'none';
    }

    function showEditModal(id, status, tanggal, alasan) {
        let form = document.getElementById('formEditProfil');
        form.action = '/super-admin/validasi-profil/' + id + '/edit';
        
        document.getElementById('edit-status').value = status;
        document.getElementById('edit-tanggal').value = tanggal ? tanggal.split(' ')[0] : '';
        document.getElementById('edit-alasan').value = alasan || '';
        
        toggleEditFields();
        document.getElementById('modalEditProfil').style.display = 'flex';
    }

    function hideEditModal() {
        document.getElementById('modalEditProfil').style.display = 'none';
    }

    function toggleEditFields() {
        let status = document.getElementById('edit-status').value;
        if (status === 'disetujui') {
            document.getElementById('edit-tanggal-container').style.display = 'block';
            document.getElementById('edit-alasan-container').style.display = 'none';
        } else {
            document.getElementById('edit-tanggal-container').style.display = 'none';
            document.getElementById('edit-alasan-container').style.display = 'block';
        }
    }

    function confirmApprove(id, jenisPrioritas) {
        if (jenisPrioritas === 'disabilitas_sementara' || jenisPrioritas === 'ibu_hamil') {
            Swal.fire({
                title: 'Setujui Prioritas Sementara',
                html: 'Masukkan batas tanggal berlaku berdasarkan dokumen dokter:<br><br><input type="date" id="swal-input-date" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const dateValue = document.getElementById('swal-input-date').value;
                    if (!dateValue) {
                        Swal.showValidationMessage('Anda harus memasukkan tanggal batas berlaku!');
                    }
                    return dateValue;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('input-date-' + id).value = result.value;
                    document.getElementById('form-approve-' + id).submit();
                }
            });
        } else {
            // Disabilitas Permanen
            Swal.fire({
                title: 'Setujui Disabilitas Permanen?',
                text: "Status ini akan berlaku selamanya.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-approve-' + id).submit();
                }
            });
        }
    }

    function confirmCetak() {
        Swal.fire({
            title: 'Konfirmasi Cetak Laporan',
            text: "Apakah Anda ingin mencantumkan QR Code verifikasi pada laporan?",
            icon: 'question',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonColor: '#0d6efd',
            denyButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Cantumkan',
            denyButtonText: 'Tidak, Kosongkan'
        }).then((result) => {
            const url = new URL('{{ route('admin.validasi_prioritas.cetak') }}');
            if (result.isConfirmed) {
                url.searchParams.set('qrcode', 1);
            } else if (result.isDenied) {
                url.searchParams.set('qrcode', 0);
            }
            
            @if(request('search')) url.searchParams.set('search', '{{ request('search') }}'); @endif
            @if(request('start_date')) url.searchParams.set('start_date', '{{ request('start_date') }}'); @endif
            @if(request('end_date')) url.searchParams.set('end_date', '{{ request('end_date') }}'); @endif
            
            window.open(url.toString(), '_blank');
        });
    }
</script>
@endsection
