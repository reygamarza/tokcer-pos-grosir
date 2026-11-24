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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id('id_pembelian');
            $table->foreignId('id_pemasok')->constrained('pemasok', 'id_pemasok')->restrictOnDelete();
            $table->foreignId('id_pengguna')->constrained('users', 'id')->restrictOnDelete();
            $table->string('no_faktur', 50)->unique();
            $table->date('tanggal');
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas', 'sebagian'])->default('belum_lunas');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
