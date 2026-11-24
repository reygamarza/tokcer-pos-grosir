<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';
    protected $primaryKey = 'id_detail';
    protected $fillable = [
        'id_penjualan', 'id_kemasan', 'qty', 'tipe_harga',
        'harga_satuan', 'diskon', 'subtotal', 'hpp_saat_transaksi'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function kemasan()
    {
        return $this->belongsTo(KemasanBarang::class, 'id_kemasan', 'id_kemasan');
    }
}
