<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiStok extends Model
{
    protected $table = 'mutasi_stok';
    protected $primaryKey = 'id_mutasi';
    protected $fillable = [
        'id_barang', 'id_pengguna', 'waktu', 'arah',
        'jenis_transaksi', 'id_referensi', 'qty', 'stok_sebelum', 'stok_sesudah'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }
}
