<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AturanHargaGrosir extends Model
{
    use LogsActivity;

    protected $table = 'aturan_harga_grosir';
    protected $primaryKey = 'id_aturan';
    protected $fillable = [
        'id_barang', 'id_kemasan', 'qty_minimum',
        'harga_grosir', 'tanggal_berlaku', 'status_aktif'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Aturan harga grosir {$eventName}");
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function kemasan()
    {
        return $this->belongsTo(KemasanBarang::class, 'id_kemasan', 'id_kemasan');
    }
}
