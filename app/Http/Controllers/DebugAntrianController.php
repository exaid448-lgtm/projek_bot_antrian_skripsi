<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

/**
 * Controller untuk debug detail masalah insert antrian
 * Jalankan: /debug-antrian
 */
class DebugAntrianController extends Controller
{
    public function debug()
    {
        echo "<h2>🔍 DEBUG ANTRIAN SYSTEM</h2>";
        echo "<hr>";

        try {
            // 1. Cek Foreign Key
            echo "<h3>1. Foreign Key Check</h3>";
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_NAME='antrian' AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (count($foreignKeys) > 0) {
                echo "<pre>";
                foreach ($foreignKeys as $fk) {
                    echo "✓ FK: {$fk->CONSTRAINT_NAME}\n";
                    echo "  {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} → {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
                }
                echo "</pre>";
            } else {
                echo "<span style='color:red'>✗ Tidak ada Foreign Key!</span><br>";
            }

            // 2. Cek struktur tabel
            echo "<h3>2. Struktur Tabel Antrian</h3>";
            $structure = DB::select("DESCRIBE antrian");
            echo "<table border='1' style='width:100%; border-collapse:collapse'>";
            echo "<tr style='background:#ccc'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            foreach ($structure as $col) {
                echo "<tr>";
                echo "<td>{$col->Field}</td>";
                echo "<td>{$col->Type}</td>";
                echo "<td>{$col->Null}</td>";
                echo "<td>{$col->Key}</td>";
                echo "<td>{$col->Default}</td>";
                echo "</tr>";
            }
            echo "</table><br>";

            // 3. Cek data konsul
            echo "<h3>3. Data Konsul (untuk FK test)</h3>";
            $konsul = DB::table('konsul')->first();
            if ($konsul) {
                echo "<pre>";
                echo "ID Konsul: " . $konsul->id_konsul . "\n";
                echo "Nama: " . $konsul->nama_pengunjung . "\n";
                echo "</pre>";
            } else {
                echo "<span style='color:red'>✗ Tidak ada data konsul!</span><br>";
            }

            // 4. Cek data loket
            echo "<h3>4. Data Loket (untuk FK test)</h3>";
            $loket = DB::table('loket')->first();
            if ($loket) {
                echo "<pre>";
                echo "ID Loket: " . $loket->id_loket . "\n";
                echo "Nama: " . $loket->nama_loket . "\n";
                echo "</pre>";
            } else {
                echo "<span style='color:red'>✗ Tidak ada data loket!</span><br>";
            }

            // 5. Test INSERT manual
            echo "<h3>5. Test INSERT Manual</h3>";
            if ($konsul && $loket) {
                try {
                    $insertId = DB::table('antrian')->insertGetId([
                        'id_konsul'       => $konsul->id_konsul,
                        'id_loket'        => $loket->id_loket,
                        'nomor_antrian'   => 100,
                        'waktu_diberikan' => now(),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                    echo "<span style='color:green'>✓ INSERT BERHASIL! ID: {$insertId}</span><br>";

                    // Verify
                    $data = DB::table('antrian')->find($insertId);
                    echo "<pre>";
                    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    echo "</pre>";

                    // Delete test
                    DB::table('antrian')->where('id_antrian', $insertId)->delete();
                    echo "<span style='color:green'>✓ Test data dihapus</span><br>";
                } catch (\Exception $e) {
                    echo "<span style='color:red'>✗ INSERT GAGAL!</span><br>";
                    echo "<pre style='color:red'>" . $e->getMessage() . "</pre>";
                }
            }

            // 6. Cek total antrian
            echo "<h3>6. Total Antrian di Database</h3>";
            $count = DB::table('antrian')->count();
            echo "Total: <strong>{$count}</strong> records<br>";

            // 7. List semua antrian
            if ($count > 0) {
                echo "<h3>7. Daftar Antrian</h3>";
                $antrians = DB::table('antrian')->get();
                echo "<table border='1' style='width:100%; border-collapse:collapse'>";
                echo "<tr style='background:#ccc'>";
                echo "<th>ID</th><th>ID Konsul</th><th>ID Loket</th><th>Nomor</th><th>Waktu Diberikan</th>";
                echo "</tr>";
                foreach ($antrians as $a) {
                    echo "<tr>";
                    echo "<td>{$a->id_antrian}</td>";
                    echo "<td>{$a->id_konsul}</td>";
                    echo "<td>{$a->id_loket}</td>";
                    echo "<td>{$a->nomor_antrian}</td>";
                    echo "<td>{$a->waktu_diberikan}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }

            echo "<hr>";
            echo "<h3>✅ Debug selesai</h3>";

        } catch (\Exception $e) {
            echo "<h3 style='color:red'>❌ ERROR:</h3>";
            echo "<pre style='color:red'>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
        }
    }
}
