<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KemasanBarang extends Model
{
    protected $table = 'kemasan_barang';
    protected $primaryKey = 'id_kemasan';
    protected $fillable = ['id_barang', 'nama_kemasan', 'kode_barcode', 'isi_per_kemasan', 'harga_jual'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function aturanGrosir()
    {
        return $this->hasMany(AturanHargaGrosir::class, 'id_kemasan', 'id_kemasan');
    }
}
