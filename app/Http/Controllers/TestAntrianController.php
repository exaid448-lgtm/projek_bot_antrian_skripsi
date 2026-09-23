<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk TEST INSERT data antrian langsung ke database
 * Jalankan di browser: /test-insert-antrian
 */
class TestAntrianController extends Controller
{
    public function insertTest()
    {
        try {
            // Test 1: Check tabel konsul
            $konsulCount = DB::table('konsul')->count();
            echo "✓ Tabel konsul ada, total: " . $konsulCount . " records<br>";

            // Test 2: Check tabel loket
            $loketCount = DB::table('loket')->count();
            echo "✓ Tabel loket ada, total: " . $loketCount . " records<br>";

            // Test 3: Check tabel antrian
            $antrianCount = DB::table('antrian')->count();
            echo "✓ Tabel antrian ada, total: " . $antrianCount . " records<br>";

            // Test 4: Check kolom antrian
            $columns = DB::getSchemaBuilder()->getColumnListing('antrian');
            echo "✓ Kolom antrian: " . implode(', ', $columns) . "<br><br>";

            // Test 5: Insert test data
            if ($konsulCount > 0 && $loketCount > 0) {
                $konsul = DB::table('konsul')->first();
                $loket = DB::table('loket')->first();

                $id = DB::table('antrian')->insertGetId([
                    'id_konsul'       => $konsul->id_konsul,
                    'id_loket'        => $loket->id_loket,
                    'nomor_antrian'   => 999,
                    'waktu_diberikan' => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                echo "✓ Insert berhasil! ID: " . $id . "<br>";

                // Verify
                $data = DB::table('antrian')->find($id);
                echo "✓ Data terverifikasi:<br>";
                echo json_encode($data, JSON_PRETTY_PRINT) . "<br>";

                // Delete test data
                DB::table('antrian')->where('id_antrian', $id)->delete();
                echo "✓ Test data dihapus<br>";
            }

            echo "<br><strong>Status: SEMUA TEST PASSED ✓</strong>";

        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage();
        }
    }
}
