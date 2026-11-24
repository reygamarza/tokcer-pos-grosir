<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Barang extends Model
{
    use LogsActivity;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $fillable = [
        'id_kategori', 'kode_sku', 'nama_barang', 'satuan',
        'hpp', 'harga_jual', 'stok_minimum', 'stok_saat_ini', 'status_aktif'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_barang', 'hpp', 'harga_jual', 'stok_minimum', 'status_aktif'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Data barang {$eventName}");
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'id_kategori', 'id_kategori');
    }

    public function kemasan()
    {
        return $this->hasMany(KemasanBarang::class, 'id_barang', 'id_barang');
    }

    public function aturanGrosir()
    {
        return $this->hasMany(AturanHargaGrosir::class, 'id_barang', 'id_barang');
    }

    public function mutasiStok()
    {
        return $this->hasMany(MutasiStok::class, 'id_barang', 'id_barang');
    }
}
