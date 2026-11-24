<?php

namespace Database\Seeders;

use App\Models\KategoriBarang;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['kode_kategori' => 'KAT-01', 'nama_kategori' => 'Sembako'],
            ['kode_kategori' => 'KAT-02', 'nama_kategori' => 'Minuman & Kopi'],
            ['kode_kategori' => 'KAT-03', 'nama_kategori' => 'Mie Instan'],
            ['kode_kategori' => 'KAT-04', 'nama_kategori' => 'Sabun & Kebutuhan Rumah'],
        ];

        foreach ($kategoris as $kat) {
            KategoriBarang::firstOrCreate(['kode_kategori' => $kat['kode_kategori']], $kat);
        }
    }
}
