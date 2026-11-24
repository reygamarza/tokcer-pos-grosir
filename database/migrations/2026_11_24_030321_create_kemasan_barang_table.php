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
        Schema::create('kemasan_barang', function (Blueprint $table) {
            $table->id('id_kemasan');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->cascadeOnDelete();
            $table->string('nama_kemasan', 50); // misal: Pcs, Pak, Dus
            $table->string('kode_barcode', 50)->nullable()->unique();
            $table->integer('isi_per_kemasan')->default(1); // Konversi ke satuan dasar
            $table->decimal('harga_jual', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kemasan_barang');
    }
};
