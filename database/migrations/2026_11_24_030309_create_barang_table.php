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
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->foreignId('id_kategori')->constrained('kategori_barang', 'id_kategori')->restrictOnDelete();
            $table->string('kode_sku', 50)->unique();
            $table->string('nama_barang', 150);
            $table->string('satuan', 30); // Satuan dasar, misal: pcs, bungkus, kg
            $table->decimal('hpp', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0); // Harga jual eceran satuan dasar
            $table->integer('stok_minimum')->default(0);
            $table->integer('stok_saat_ini')->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
