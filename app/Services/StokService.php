<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\MutasiStok;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class StokService
{
    /**
     * Menambah stok barang (Pembelian, Retur dari Pelanggan, Opname Plus)
     *
     * @param int $idBarang ID barang (satuan dasar)
     * @param int $qty Jumlah dalam satuan dasar
     * @param string $jenisTransaksi ('pembelian'|'retur'|'opname')
     * @param int $idReferensi ID transaksi sumber (misal id_pembelian)
     * @param int|null $idPengguna ID user pelaksana
     */
    public function tambahStok(
        int $idBarang,
        int $qty,
        string $jenisTransaksi,
        int $idReferensi,
        ?int $idPengguna = null
    ): Barang {
        if ($qty <= 0) {
            throw new Exception("Jumlah penambahan stok harus lebih dari 0.");
        }

        return DB::transaction(function () use ($idBarang, $qty, $jenisTransaksi, $idReferensi, $idPengguna) {
            // Kunci baris barang selama transaksi berlangsung (ACID compliance - NFR-08)
            $barang = Barang::where('id_barang', $idBarang)->lockForUpdate()->firstOrFail();

            $stokSebelum = $barang->stok_saat_ini;
            $stokSesudah = $stokSebelum + $qty;

            $barang->update([
                'stok_saat_ini' => $stokSesudah,
            ]);

            MutasiStok::create([
                'id_barang' => $barang->id_barang,
                'id_pengguna' => $idPengguna ?? Auth::id() ?? 1,
                'waktu' => now(),
                'arah' => 'masuk',
                'jenis_transaksi' => $jenisTransaksi,
                'id_referensi' => $idReferensi,
                'qty' => $qty,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
            ]);

            return $barang;
        });
    }

    /**
     * Mengurangi stok barang (Penjualan Kasir, Pesanan Online, Retur ke Pemasok, Opname Minus)
     */
    public function kurangiStok(
        int $idBarang,
        int $qty,
        string $jenisTransaksi,
        int $idReferensi,
        ?int $idPengguna = null
    ): Barang {
        if ($qty <= 0) {
            throw new Exception("Jumlah pengurangan stok harus lebih dari 0.");
        }

        return DB::transaction(function () use ($idBarang, $qty, $jenisTransaksi, $idReferensi, $idPengguna) {
            $barang = Barang::where('id_barang', $idBarang)->lockForUpdate()->firstOrFail();

            $stokSebelum = $barang->stok_saat_ini;

            // Validasi NFR-08: Stok barang tidak boleh bernilai negatif
            if ($stokSebelum < $qty) {
                throw new Exception("Stok {$barang->nama_barang} tidak mencukupi! Sisa stok: {$stokSebelum} {$barang->satuan}, dibutuhkan: {$qty} {$barang->satuan}.");
            }

            $stokSesudah = $stokSebelum - $qty;

            $barang->update([
                'stok_saat_ini' => $stokSesudah,
            ]);

            MutasiStok::create([
                'id_barang' => $barang->id_barang,
                'id_pengguna' => $idPengguna ?? Auth::id() ?? 1,
                'waktu' => now(),
                'arah' => 'keluar',
                'jenis_transaksi' => $jenisTransaksi,
                'id_referensi' => $idReferensi,
                'qty' => $qty,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
            ]);

            return $barang;
        });
    }
}
