<?php

use App\Http\Controllers\Petugas\QrisController;
use App\Http\Controllers\Petugas\RekeningController;
use App\Http\Controllers\Petugas\VoucherController;
use App\Http\Controllers\Petugas\AkunController;
use App\Http\Controllers\Petugas\UserController;
use App\Http\Controllers\Petugas\DashboardController;
use App\Http\Controllers\Petugas\DeliveryController;
use App\Http\Controllers\Petugas\OrderController;
use App\Http\Controllers\Pengguna\AlamatController;
use App\Http\Controllers\Pengguna\PesananController;
use App\Http\Controllers\Pengguna\ProfilController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\Petugas\DeliveryZoneController;
use App\Http\Controllers\Petugas\ServiceController;
use Illuminate\Support\Facades\Route;

// Publik
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/layanan/{slug}', [BerandaController::class, 'layanan'])->name('layanan.detail');

// Hanya untuk yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Area pengguna
Route::middleware(['auth', 'role:pengguna'])->group(function () {

    // Pesanan baru
    Route::get('/pesanan/buat', [PesananController::class, 'create'])->name('pesanan.buat');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.simpan');
    Route::post('/pesanan/voucher', [\App\Http\Controllers\Pengguna\VoucherController::class, 'cek'])
        ->middleware('throttle:30,1')->name('pesanan.voucher');

    // Profil
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'password'])->name('profil.password');
    Route::get('/profil/akun', [ProfilController::class, 'akun'])->name('profil.akun');

    // Pengaturan
    Route::get('/pengaturan', [\App\Http\Controllers\Pengguna\PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/ganti-akun', [\App\Http\Controllers\Pengguna\PengaturanController::class, 'gantiAkun'])->name('pengaturan.ganti');

   Route::get('/chat', [\App\Http\Controllers\Pengguna\ChatController::class, 'index'])->name('chat.index');
   Route::get('/chat/pesanan/{order}', [\App\Http\Controllers\Pengguna\ChatController::class, 'show'])->name('chat.order');
   Route::post('/chat/pesanan/{order}', [\App\Http\Controllers\Pengguna\ChatController::class, 'store'])->middleware('throttle:30,1')->name('chat.kirim');
   Route::get('/chat/pesanan/{order}/pesan', [\App\Http\Controllers\Pengguna\ChatController::class, 'sejak'])->name('chat.sejak');

    // Alamat tersimpan
    Route::get('/profil/alamat', [AlamatController::class, 'index'])->name('alamat.index');
    Route::post('/profil/alamat', [AlamatController::class, 'store'])->name('alamat.simpan');
    Route::put('/profil/alamat/{alamat}', [AlamatController::class, 'update'])->name('alamat.update');
    Route::patch('/profil/alamat/{alamat}/utama', [AlamatController::class, 'utama'])->name('alamat.utama');
    Route::delete('/profil/alamat/{alamat}', [AlamatController::class, 'destroy'])->name('alamat.destroy');

    // Riwayat pesanan
    Route::get('/profil/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/profil/pesanan/{order}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::patch('/profil/pesanan/{order}/batal', [PesananController::class, 'batal'])->name('pesanan.batal');
});

// Area petugas
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // Chat pengguna (per pesanan)
        Route::get('chat/{order}', [\App\Http\Controllers\Petugas\ChatController::class, 'show'])->name('chat.show');
        Route::post('chat/{order}', [\App\Http\Controllers\Petugas\ChatController::class, 'store'])->middleware('throttle:60,1')->name('chat.kirim');
        Route::get('chat/{order}/pesan', [\App\Http\Controllers\Petugas\ChatController::class, 'sejak'])->name('chat.sejak');

        // Pesanan
        Route::get('pesanan', [OrderController::class, 'index'])->name('pesanan.index');
        Route::get('pesanan/{order}', [OrderController::class, 'show'])->name('pesanan.show');
        Route::get('pesanan/{order}/cetak', [OrderController::class, 'cetak'])->name('pesanan.cetak');
        Route::post('pesanan/{order}/lanjut', [OrderController::class, 'lanjut'])->name('pesanan.lanjut');
        Route::post('pesanan/{order}/batal', [OrderController::class, 'batal'])->name('pesanan.batal');
        Route::post('pesanan/{order}/bayar', [OrderController::class, 'bayar'])->name('pesanan.bayar');
        Route::post('item/{item}', [OrderController::class, 'item'])->name('item.update');

        // Jemput & antar
        Route::get('pengantaran', [DeliveryController::class, 'index'])->name('pengantaran.index');
        Route::patch('pengantaran/{delivery}', [DeliveryController::class, 'update'])->name('pengantaran.update');
        Route::post('pengantaran/{delivery}/mulai', [DeliveryController::class, 'mulai'])->name('pengantaran.mulai');
        Route::post('pengantaran/{delivery}/selesai', [DeliveryController::class, 'selesai'])->name('pengantaran.selesai');

        // Pengguna & akun petugas
        Route::get('pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::get('pengguna/{user}', [UserController::class, 'show'])->name('pengguna.show');

        Route::get('akun', [AkunController::class, 'edit'])->name('akun');
        Route::put('akun', [AkunController::class, 'update'])->name('akun.update');
        Route::put('akun/password', [AkunController::class, 'password'])->name('akun.password');

        // Master data layanan
        Route::patch('layanan/{service}/toggle', [ServiceController::class, 'toggle'])->name('layanan.toggle');
        Route::resource('layanan', ServiceController::class)
            ->parameters(['layanan' => 'service'])
            ->except('show');

        // Master data voucher
        Route::patch('voucher/{voucher}/toggle', [VoucherController::class, 'toggle'])->name('voucher.toggle');
        Route::resource('voucher', VoucherController::class)->except('show');

        // Master data rekening bank
        Route::patch('rekening/{rekening}/toggle', [RekeningController::class, 'toggle'])->name('rekening.toggle');
        Route::resource('rekening', RekeningController::class)->except('show');

        // Master data QRIS
        Route::patch('qris/{qris}/toggle', [QrisController::class, 'toggle'])->name('qris.toggle');
        Route::resource('qris', QrisController::class)
            ->parameters(['qris' => 'qris'])
            ->except('show');

        // Master data zona
        Route::patch('zona/{zone}/toggle', [DeliveryZoneController::class, 'toggle'])->name('zona.toggle');
        Route::resource('zona', DeliveryZoneController::class)
            ->parameters(['zona' => 'zone'])
            ->except('show');
    });