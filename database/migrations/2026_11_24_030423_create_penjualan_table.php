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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->foreignId('id_kasir')->constrained('users', 'id')->restrictOnDelete();
            $table->unsignedBigInteger('id_pelanggan')->nullable(); // Foreign key disambungkan di Fase 2
            $table->string('no_transaksi', 50)->unique();
            $table->dateTime('tanggal');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('jumlah_bayar', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->enum('metode_pembayaran', ['tunai', 'non_tunai', 'tempo'])->default('tunai');
            $table->enum('jenis_transaksi', ['tunai', 'kredit'])->default('tunai');
            $table->enum('status', ['selesai', 'batal'])->default('selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
