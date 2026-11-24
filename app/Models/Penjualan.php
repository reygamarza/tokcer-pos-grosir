<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $fillable = [
        'id_kasir', 'id_pelanggan', 'no_transaksi', 'tanggal',
        'subtotal', 'diskon', 'total', 'jumlah_bayar', 'kembalian',
        'metode_pembayaran', 'jenis_transaksi', 'status'
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_kasir', 'id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan', 'id_penjualan');
    }
}
