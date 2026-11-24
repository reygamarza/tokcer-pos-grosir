<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mutasi_stok', function (Blueprint $table) {
            $table->id('id_mutasi');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->restrictOnDelete();
            $table->foreignId('id_pengguna')->constrained('users', 'id')->restrictOnDelete();
            $table->dateTime('waktu');
            $table->enum('arah', ['masuk', 'keluar']);
            $table->enum('jenis_transaksi', ['pembelian', 'penjualan', 'pesanan_online', 'retur', 'opname']);
            $table->unsignedBigInteger('id_referensi');
            $table->integer('qty'); // Dalam satuan dasar
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok');
    }
};
