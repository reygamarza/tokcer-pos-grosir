<?php

use App\Http\Controllers\Gudang\StokController;
use App\Http\Controllers\Owner\BarangController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Gudang\FormPembelian;
use App\Livewire\Kasir\PosKasir;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Route;

// 1. HALAMAN PUBLIK & KATALOG PELANGGAN (FR-17)
Route::get('/', function () {
    return view('welcome'); // Nanti diganti KatalogController
})->name('katalog.index');

// 2. DASHBOARD INTERNAL (Semua staf internal toko)
Route::middleware(['auth', 'role:Owner|Finance|Gudang|Kasir'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// 3. MODUL OWNER (Data Master, Harga Grosir, Manajemen User, Audit Trail)
Route::middleware(['auth', 'role:Owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::resource('barang', BarangController::class)->except(['show', 'edit', 'update']);
});

// 4. MODUL KASIR POS (FR-06, FR-07, FR-08)
Route::middleware(['auth', 'role:Owner|Kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', PosKasir::class)->name('pos');
    Route::get('/struk/{penjualan}', function (Penjualan $penjualan) {
        $penjualan->load(['kasir', 'detail.kemasan.barang']);
        return view('kasir.struk', compact('penjualan'));
    })->name('struk');
});

// 5. MODUL GUDANG & STOK (FR-04, FR-05, FR-20, FR-21, FR-24)
Route::middleware(['auth', 'role:Owner|Gudang'])->prefix('gudang')->name('gudang.')->group(function () {
    Route::get('/pembelian', [StokController::class, 'indexPembelian'])->name('pembelian.index');
    Route::get('/pembelian/tambah', FormPembelian::class)->name('pembelian.create');
    Route::get('/mutasi-stok', [StokController::class, 'indexMutasi'])->name('mutasi.index');
});

// 6. MODUL KEUANGAN (FR-11 s/d FR-15)
Route::middleware(['auth', 'role:Owner|Finance'])->prefix('keuangan')->name('keuangan.')->group(function () {
    // Rute Utang-Piutang, Pelunasan, Kas Lain, Laporan Keuangan
});

// 7. MODUL PELANGGAN ONLINE (FR-18, FR-19)
Route::middleware(['auth', 'role:Pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    // Rute Keranjang, Checkout Midtrans, Riwayat Pesanan
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
