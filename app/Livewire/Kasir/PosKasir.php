<?php

namespace App\Livewire\Kasir;

use App\Models\KemasanBarang;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Services\HargaGrosirService;
use App\Services\StokService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Exception;

class PosKasir extends Component
{
    public string $search = '';
    public array $cart = []; // Keranjang belanja: [id_kemasan => item_data]
    public float $diskonTransaksi = 0;
    public float $jumlahBayar = 0;
    public string $metodePembayaran = 'tunai';

    // Dipanggil otomatis saat kasir scan barcode USB lalu scanner mengirim tombol Enter
    public function scanAtauPilihBarcode()
    {
        $keyword = trim($this->search);
        if (empty($keyword)) return;

        // 1. Cari exact match barcode atau SKU
        $kemasan = KemasanBarang::with('barang')
            ->where('kode_barcode', $keyword)
            ->orWhereHas('barang', fn($q) => $q->where('kode_sku', $keyword))
            ->first();

        // 2. Fallback: ambil kemasan pertama yang namanya mirip keyword
        if (!$kemasan) {
            $kemasan = KemasanBarang::with('barang')
                ->whereHas('barang', fn($q) => $q->where('status_aktif', true)->where('nama_barang', 'like', "%{$keyword}%"))
                ->first();
        }

        if ($kemasan) {
            $this->tambahKeKeranjang($kemasan->id_kemasan);
            $this->search = '';
        } else {
            session()->flash('error_kasir', "Barang '{$keyword}' tidak ditemukan!");
        }
    }

    public function tambahKeKeranjang(int $idKemasan, int $qtyTambah = 1)
    {
        $qtyBaru = isset($this->cart[$idKemasan])
            ? $this->cart[$idKemasan]['qty'] + $qtyTambah
            : $qtyTambah;

        $this->updateQty($idKemasan, $qtyBaru);
    }

    public function updateQty(int $idKemasan, int $qtyBaru)
    {
        if ($qtyBaru <= 0) {
            unset($this->cart[$idKemasan]);
            return;
        }

        $hargaService = app(HargaGrosirService::class);
        $hasilHitung = $hargaService->hitungHarga($idKemasan, $qtyBaru);

        // Cek ketersediaan stok satuan dasar (NFR-08)
        $kemasan = KemasanBarang::with('barang')->findOrFail($idKemasan);
        if ($kemasan->barang->stok_saat_ini < $hasilHitung['total_satuan_dasar']) {
            session()->flash('error_kasir', "Stok {$kemasan->barang->nama_barang} tidak cukup! Sisa: {$kemasan->barang->stok_saat_ini} {$kemasan->barang->satuan}");
            return;
        }

        $this->cart[$idKemasan] = $hasilHitung;
    }

    public function hapusItem(int $idKemasan)
    {
        unset($this->cart[$idKemasan]);
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function getTotalAkhirProperty(): float
    {
        return max(0, $this->subtotal - $this->diskonTransaksi);
    }

    public function getKembalianProperty(): float
    {
        if ($this->metodePembayaran !== 'tunai') return 0;
        return max(0, $this->jumlahBayar - $this->totalAkhir);
    }

    public function prosesBayar()
    {
        if (empty($this->cart)) {
            session()->flash('error_kasir', 'Keranjang belanja masih kosong!');
            return;
        }

        if ($this->metodePembayaran === 'tunai' && $this->jumlahBayar < $this->totalAkhir) {
            session()->flash('error_kasir', 'Nominal uang tunai kurang dari total belanja!');
            return;
        }

        try {
            $penjualan = DB::transaction(function () {
                $stokService = app(StokService::class);

                $noTransaksi = 'TRX-' . now()->format('Ymd-His') . '-' . rand(10, 99);
                $bayar = $this->metodePembayaran === 'tunai' ? $this->jumlahBayar : $this->totalAkhir;

                $penjualan = Penjualan::create([
                    'id_kasir'          => Auth::id(),
                    'no_transaksi'      => $noTransaksi,
                    'tanggal'           => now(),
                    'subtotal'          => $this->subtotal,
                    'diskon'            => $this->diskonTransaksi,
                    'total'             => $this->totalAkhir,
                    'jumlah_bayar'      => $bayar,
                    'kembalian'         => $this->kembalian,
                    'metode_pembayaran' => $this->metodePembayaran,
                    'jenis_transaksi'   => 'tunai',
                    'status'            => 'selesai',
                ]);

                foreach ($this->cart as $item) {
                    DetailPenjualan::create([
                        'id_penjualan'       => $penjualan->id_penjualan,
                        'id_kemasan'         => $item['id_kemasan'],
                        'qty'                => $item['qty'],
                        'tipe_harga'         => $item['tipe_harga'],
                        'harga_satuan'       => $item['harga_satuan'],
                        'diskon'             => 0,
                        'subtotal'           => $item['subtotal'],
                        'hpp_saat_transaksi' => $item['hpp_saat_transaksi'],
                    ]);

                    // Potong stok satuan dasar & catat ke mutasi_stok otomatis (FR-09)
                    $stokService->kurangiStok(
                        $item['id_barang'],
                        $item['total_satuan_dasar'],
                        'penjualan',
                        $penjualan->id_penjualan,
                        Auth::id()
                    );
                }

                return $penjualan;
            });

            // Reset form kasir & arahkan ke halaman cetak nota thermal
            $this->reset(['cart', 'diskonTransaksi', 'jumlahBayar', 'search']);
            return redirect()->route('kasir.struk', $penjualan->id_penjualan);

        } catch (Exception $e) {
            session()->flash('error_kasir', $e->getMessage());
        }
    }

    public function render()
    {
        $hasilPencarian = [];
        if (strlen(trim($this->search)) >= 2) {
            $hasilPencarian = KemasanBarang::with('barang')
                ->whereHas('barang', function ($q) {
                    $q->where('status_aktif', true)
                      ->where('nama_barang', 'like', '%' . $this->search . '%');
                })
                ->limit(8)
                ->get();
        }

        return view('livewire.kasir.pos-kasir', [
            'hasilPencarian' => $hasilPencarian,
        ]);
    }
}
