<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Loket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DataJadwalController extends Controller
{
public function index(Request $request)
{
    $lokets = Loket::select('id_loket', 'nama_loket', 'nama_pelayanan')->get();

    // Perbaikan query enum agar tidak error PDO
    $column = DB::select("SHOW COLUMNS FROM jadwal WHERE Field = 'setatus'");
    $statuses = [];
    if (!empty($column)) {
        preg_match('/^enum\((.*)\)$/', $column[0]->Type, $matches);
        $statuses = array_map(fn($v) => trim($v, "'"), explode(',', $matches[1]));
    }

    // Gunakan with('profil.loket') agar tidak error "property on null"
    $query = Jadwal::with(['profil.loket']);

    if ($request->search) {
        $query->whereHas('profil', function ($q) use ($request) {
            $q->where('nama_user', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->loket) {
        $query->whereHas('profil.loket', function ($q) use ($request) {
            $q->where('nama_loket', $request->loket);
        });
    }

    if ($request->start_date) {
        $query->whereDate('tanggal', '>=', $request->start_date);
    }

    if ($request->end_date) {
        $query->whereDate('tanggal', '<=', $request->end_date);
    }

    $jadwals = $query->orderBy('tanggal', 'desc')->get();

    return view('administrator.data_jadwal', compact('jadwals', 'lokets', 'statuses'));
}

public function store(Request $request)
{
    $request->validate([
        'id_karyawan' => 'required', // Sesuaikan dengan <select name="id_karyawan">
        'tanggal'     => 'required|date',
        'jam_masuk'   => 'required',
        'jam_pulang'  => 'required',
        'shift'       => 'required',
        'status'      => 'required', 
    ]);

// 2. Simpan ke Database
    Jadwal::create([
        'id_profil'  => $request->id_karyawan, // AMBIL dari id_karyawan, SIMPAN ke id_profil
        'tanggal'    => $request->tanggal,
        'jam_masuk'  => $request->jam_masuk,
        'jam_pulang' => $request->jam_pulang,
        'shift'      => $request->shift,
        'setatus'    => $request->status, 
    ]);

    return back()->with('success', 'Jadwal berhasil ditambah');
}
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_loket'    => 'required', // Tetap divalidasi agar user milih loket
            'id_karyawan' => 'required',
            'tanggal'     => 'required|date',
            'jam_masuk'   => 'required',
            'jam_pulang'  => 'required',
            'shift'       => 'required',
            'status'      => 'required',
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update([
            'id_profil'  => $request->id_karyawan, // ID Karyawan masuk ke id_profil
            'tanggal'    => $request->tanggal,
            'jam_masuk'  => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
            'shift'      => $request->shift,
            'setatus'    => $request->status,
        ]);

        return back()->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy($id)
    {
        Jadwal::destroy($id);
        return back()->with('success', 'Jadwal dihapus');
    }

    public function getKaryawanByLoket($id_loket)
    {
        $data = User::whereHas('profil', function ($q) use ($id_loket) {
            $q->where('id_loket', $id_loket);
        })->with('profil')->get();

        return response()->json(
            $data->map(fn($u) => [
                'id'   => $u->profil->id_profil,
                'name' => $u->profil->nama_user
            ])
        );
    }
    public function cetak(Request $request)
{
    $query = Jadwal::with(['profil.loket']);

    // Filter Nama Karyawan
    if ($request->search) {
        $query->whereHas('profil', function ($q) use ($request) {
            $q->where('nama_user', 'like', '%' . $request->search . '%');
        });
    }

    // Filter Loket
    if ($request->loket) {
        $query->whereHas('profil.loket', function ($q) use ($request) {
            $q->where('nama_loket', $request->loket);
        });
    }

    // Filter Tanggal
    if ($request->start_date) {
        $query->whereDate('tanggal', '>=', $request->start_date);
    }

    if ($request->end_date) {
        $query->whereDate('tanggal', '<=', $request->end_date);
    }

    $jadwals = $query->orderBy('tanggal', 'asc')->get();

    // Menggunakan view laporan_jadwal yang sudah Anda sediakan
    return view('pdf.laporan_jadwal', compact('jadwals'));
}
}
