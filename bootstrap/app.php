<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/pengunjung', 
            users: '/' // Ubah dari /dashboard ke / agar tidak error mental
        );

        $middleware->alias([
            'ceklogin' => \App\Http\Middleware\CekLogin::class,
            'role'     => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        // CSRF exception
        $middleware->validateCsrfTokens(except: [
            'antrian/store-voice',
        ]);

        // Middleware alias
        $middleware->alias([
            'ceklogin' => \App\Http\Middleware\CekLogin::class,
            'role'     => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        // Tambahkan ini jika masih mental
        $middleware->trustProxies(at: '*');
        // ❌ JANGAN PAKAI statefulApi() UNTUK LOGIN WEB
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Pencatatan otomatis kegagalan koneksi Database (Revisi Penguji)
        $exceptions->report(function (\PDOException $e) {
            \Illuminate\Support\Facades\Log::critical("[SYSTEM_FAILURE] [Komponen: Database MySQL] [Tingkat: Critical] [Aksi: Mode Darurat / Kontak Admin] Pesan: Koneksi database terputus atau MySQL mati: " . $e->getMessage());
        });
        $exceptions->report(function (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::critical("[SYSTEM_FAILURE] [Komponen: Database MySQL] [Tingkat: Critical] [Aksi: Mode Darurat / Kontak Admin] Pesan: Gangguan query database: " . $e->getMessage());
        });
    })->create();
