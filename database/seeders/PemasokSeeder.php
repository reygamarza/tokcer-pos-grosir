<?php

namespace Database\Seeders;

use App\Models\Pemasok;
use Illuminate\Database\Seeder;

class PemasokSeeder extends Seeder
{
    public function run(): void
    {
        $pemasoks = [
            [
                'nama_pemasok' => 'PT Indomarco Adi Prima',
                'kontak' => '081234567890',
                'alamat' => 'Jl. Soekarno Hatta No. 45, Bandung',
                'status_aktif' => true,
            ],
            [
                'nama_pemasok' => 'CV Sumber Sembako Jaya',
                'kontak' => '082198765432',
                'alamat' => 'Pasar Induk Caringin Blok C-12, Bandung',
                'status_aktif' => true,
            ],
        ];

        foreach ($pemasoks as $p) {
            Pemasok::firstOrCreate(['nama_pemasok' => $p['nama_pemasok']], $p);
        }
    }
}
