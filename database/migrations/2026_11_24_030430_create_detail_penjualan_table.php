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
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_penjualan')->constrained('penjualan', 'id_penjualan')->cascadeOnDelete();
            $table->foreignId('id_kemasan')->constrained('kemasan_barang', 'id_kemasan')->restrictOnDelete();
            $table->integer('qty');
            $table->enum('tipe_harga', ['eceran', 'grosir'])->default('eceran');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('hpp_saat_transaksi', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
