<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ValidasiController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->query('token');
        $dataValidasi = null;
        $isValid = false;

        if ($token) {
            try {
                $decrypted = Crypt::decryptString($token);
                $dataValidasi = json_decode($decrypted, true);
                
                // Jika berhasil di-decode dan struktur JSON-nya benar
                if (is_array($dataValidasi)) {
                    $isValid = true;
                }
            } catch (DecryptException $e) {
                // Token tidak valid atau dimanipulasi
                $isValid = false;
            }
        }

        return view('validasi-digital.validasi_qrcode', compact('isValid', 'dataValidasi'));
    }
}
