<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\KemasanBarang;
use App\Models\AturanHargaGrosir;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $katMie = KategoriBarang::where('kode_kategori', 'KAT-03')->first();
        $katSembako = KategoriBarang::where('kode_kategori', 'KAT-01')->first();

        // 1. Indomie Mi Goreng Original
        $indomie = Barang::firstOrCreate(
            ['kode_sku' => 'SKU-IDM-001'],
            [
                'id_kategori' => $katMie->id_kategori,
                'nama_barang' => 'Indomie Mi Goreng Original',
                'satuan' => 'pcs',
                'hpp' => 2700,
                'harga_jual' => 3500,
                'stok_minimum' => 40,
                'stok_saat_ini' => 200,
                'status_aktif' => true,
            ]
        );

        $indomiePcs = KemasanBarang::firstOrCreate(
            ['kode_barcode' => '8998866200578'],
            [
                'id_barang' => $indomie->id_barang,
                'nama_kemasan' => 'Pcs (Satuan)',
                'isi_per_kemasan' => 1,
                'harga_jual' => 3500,
            ]
        );

        $indomieDus = KemasanBarang::firstOrCreate(
            ['kode_barcode' => '8998866200579'],
            [
                'id_barang' => $indomie->id_barang,
                'nama_kemasan' => 'Dus (Isi 40)',
                'isi_per_kemasan' => 40,
                'harga_jual' => 118000,
            ]
        );

        AturanHargaGrosir::firstOrCreate(
            ['id_barang' => $indomie->id_barang, 'id_kemasan' => $indomiePcs->id_kemasan, 'qty_minimum' => 5],
            ['harga_grosir' => 3200, 'status_aktif' => true]
        );

        AturanHargaGrosir::firstOrCreate(
            ['id_barang' => $indomie->id_barang, 'id_kemasan' => $indomieDus->id_kemasan, 'qty_minimum' => 3],
            ['harga_grosir' => 115000, 'status_aktif' => true]
        );

        // 2. Minyak Goreng Bimoli 1 Liter
        $minyak = Barang::firstOrCreate(
            ['kode_sku' => 'SKU-MYK-001'],
            [
                'id_kategori' => $katSembako->id_kategori,
                'nama_barang' => 'Minyak Goreng Bimoli 1 Liter',
                'satuan' => 'pouch',
                'hpp' => 16500,
                'harga_jual' => 19000,
                'stok_minimum' => 12,
                'stok_saat_ini' => 48,
                'status_aktif' => true,
            ]
        );

        KemasanBarang::firstOrCreate(
            ['kode_barcode' => '8992696401112'],
            [
                'id_barang' => $minyak->id_barang,
                'nama_kemasan' => 'Pouch 1L',
                'isi_per_kemasan' => 1,
                'harga_jual' => 19000,
            ]
        );

        KemasanBarang::firstOrCreate(
            ['kode_barcode' => '8992696401113'],
            [
                'id_barang' => $minyak->id_barang,
                'nama_kemasan' => 'Dus (Isi 12 Pouch)',
                'isi_per_kemasan' => 12,
                'harga_jual' => 216000,
            ]
        );
    }
}
