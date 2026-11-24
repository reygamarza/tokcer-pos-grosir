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
        Schema::create('aturan_harga_grosir', function (Blueprint $table) {
            $table->id('id_aturan');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->cascadeOnDelete();
            $table->foreignId('id_kemasan')->nullable()->constrained('kemasan_barang', 'id_kemasan')->cascadeOnDelete();
            $table->integer('qty_minimum');
            $table->decimal('harga_grosir', 15, 2);
            $table->date('tanggal_berlaku')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_harga_grosir');
    }
};
