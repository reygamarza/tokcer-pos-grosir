<?php

namespace App\Services;

use App\Models\KemasanBarang;
use App\Models\AturanHargaGrosir;

class HargaGrosirService
{
    /**
     * Menghitung harga satuan (eceran vs grosir) berdasarkan kemasan dan qty beli.
     *
     * @param int $idKemasan
     * @param int $qty Jumlah kemasan yang dibeli
     * @return array{
     *   id_kemasan: int,
     *   id_barang: int,
     *   nama_barang: string,
     *   nama_kemasan: string,
     *   isi_per_kemasan: int,
     *   qty: int,
     *   total_satuan_dasar: int,
     *   tipe_harga: string,
     *   harga_satuan: float,
     *   subtotal: float,
     *   hpp_saat_transaksi: float
     * }
     */
    public function hitungHarga(int $idKemasan, int $qty): array
    {
        $kemasan = KemasanBarang::with('barang')->findOrFail($idKemasan);
        $barang = $kemasan->barang;
        $today = now()->toDateString();

        // Cari aturan grosir spesifik untuk kemasan ini terlebih dahulu
        $aturan = AturanHargaGrosir::where('id_barang', $barang->id_barang)
            ->where('id_kemasan', $kemasan->id_kemasan)
            ->where('status_aktif', true)
            ->where('qty_minimum', '<=', $qty)
            ->where(function ($query) use ($today) {
                $query->whereNull('tanggal_berlaku')
                      ->orWhere('tanggal_berlaku', '<=', $today);
            })
            ->orderByDesc('qty_minimum') // Ambil tier grosir tertinggi yang memenuhi syarat
            ->first();

        // Jika tidak ada aturan spesifik kemasan, cek aturan grosir berbasis satuan dasar barang
        if (!$aturan && $kemasan->isi_per_kemasan == 1) {
            $aturan = AturanHargaGrosir::where('id_barang', $barang->id_barang)
                ->whereNull('id_kemasan')
                ->where('status_aktif', true)
                ->where('qty_minimum', '<=', $qty)
                ->where(function ($query) use ($today) {
                    $query->whereNull('tanggal_berlaku')
                          ->orWhere('tanggal_berlaku', '<=', $today);
                })
                ->orderByDesc('qty_minimum')
                ->first();
        }

        if ($aturan) {
            $tipeHarga = 'grosir';
            $hargaSatuan = (float) $aturan->harga_grosir;
        } else {
            $tipeHarga = 'eceran';
            $hargaSatuan = (float) $kemasan->harga_jual;
        }

        // HPP per kemasan = HPP satuan dasar * isi_per_kemasan
        $hppKemasan = (float) ($barang->hpp * $kemasan->isi_per_kemasan);

        return [
            'id_kemasan'         => $kemasan->id_kemasan,
            'id_barang'          => $barang->id_barang,
            'nama_barang'        => $barang->nama_barang,
            'nama_kemasan'       => $kemasan->nama_kemasan,
            'isi_per_kemasan'    => $kemasan->isi_per_kemasan,
            'qty'                => $qty,
            'total_satuan_dasar' => $qty * $kemasan->isi_per_kemasan,
            'tipe_harga'         => $tipeHarga,
            'harga_satuan'       => $hargaSatuan,
            'subtotal'           => $hargaSatuan * $qty,
            'hpp_saat_transaksi' => $hppKemasan,
        ];
    }
}
