@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Data Antrian Loket</h3>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-primary text-center">
                    <tr>
                        <th>No</th>
                        <th>Nomor Antrian</th>
                        <th>Nama Pengunjung</th>
                        <th>Keperluan</th>
                        <th>Waktu Diberikan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($antrian as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center"><strong>{{ str_pad($item->nomor_antrian, 3, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>{{ $item->konsul->nama_pengunjung }}</td>
                        <td>{{ $item->konsul->konsultasi }}</td>
                        <td>{{ $item->waktu_diberikan->format('d-m-Y H:i') }}</td>
                        <td class="text-center">
                            @if ($item->antrian_selesai)
                                <span class="badge bg-success">SELESAI</span>
                            @elseif ($item->antrian_mulai)
                                <span class="badge bg-warning">SEDANG DILAYANI</span>
                            @else
                                <span class="badge bg-info">MENUNGGU</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if (!$item->antrian_mulai)
                            <button class="btn btn-sm btn-primary" onclick="startQueue({{ $item->id_antrian }})">Mulai</button>
                            @endif
                            @if ($item->antrian_mulai && !$item->antrian_selesai)
                            <button class="btn btn-sm btn-success" onclick="selesaiAntrian({{ $item->id_antrian }})">Selesai</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Data antrian tidak tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function mulaiAntrian(id) {
    if (confirm('Mulai melayani antrian ini?')) {
        window.location.href = '/antrian/' + id + '/mulai';
    }
}

function selesaiAntrian(id) {
    if (confirm('Selesaikan antrian ini?')) {
        window.location.href = '/antrian/' + id + '/selesai';
    }
}
</script>
@endsection
