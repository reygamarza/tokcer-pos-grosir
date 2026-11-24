<?php

namespace App\Livewire\Gudang;

use App\Models\Barang;
use App\Models\KemasanBarang;
use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Services\StokService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Exception;

class FormPembelian extends Component
{
    public $id_pemasok = '';
    public $no_faktur = '';
    public $tanggal = '';
    public $status_pembayaran = 'lunas';
    public $tanggal_jatuh_tempo = '';

    // Array baris barang yang dibeli dari pemasok
    public array $items = [];

    public function mount()
    {
        $this->tanggal = now()->toDateString();
        $this->no_faktur = 'PB-' . now()->format('Ymd') . '-' . rand(100, 999);
        $this->tambahBaris(); // Otomatis sediakan 1 baris kosong
    }

    public function tambahBaris()
    {
        $this->items[] = [
            'id_kemasan' => '',
            'qty' => 1,
            'harga_beli_kemasan' => 0,
        ];
    }

    public function hapusBaris(int $index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    // Otomatis isi estimasi harga beli saat kemasan dipilih
    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'id_kemasan' && !empty($value)) {
            $index = $parts[0];
            $kemasan = KemasanBarang::with('barang')->find($value);
            if ($kemasan) {
                $this->items[$index]['harga_beli_kemasan'] = $kemasan->barang->hpp * $kemasan->isi_per_kemasan;
            }
        }
    }

    public function getTotalFakturProperty(): float
    {
        return collect($this->items)->sum(function ($item) {
            return (float) ($item['qty'] ?? 0) * (float) ($item['harga_beli_kemasan'] ?? 0);
        });
    }

    public function simpanPembelian()
    {
        $this->validate([
            'id_pemasok' => 'required|exists:pemasok,id_pemasok',
            'no_faktur' => 'required|string|max:50|unique:pembelian,no_faktur',
            'tanggal' => 'required|date',
            'status_pembayaran' => 'required|in:lunas,belum_lunas',
            'tanggal_jatuh_tempo' => 'required_if:status_pembayaran,belum_lunas|nullable|date',
            'items.*.id_kemasan' => 'required|exists:kemasan_barang,id_kemasan',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli_kemasan' => 'required|numeric|min:0',
        ], [
            'id_pemasok.required' => 'Pilih pemasok terlebih dahulu.',
            'items.*.id_kemasan.required' => 'Pilih barang & kemasan pada semua baris.',
            'tanggal_jatuh_tempo.required_if' => 'Tanggal jatuh tempo wajib diisi jika pembelian kredit/tempo.',
        ]);

        try {
            DB::transaction(function () {
                $stokService = app(StokService::class);

                // 1. Simpan Header Faktur Pembelian (FR-04)
                $pembelian = Pembelian::create([
                    'id_pemasok'          => $this->id_pemasok,
                    'id_pengguna'         => Auth::id(),
                    'no_faktur'           => $this->no_faktur,
                    'tanggal'             => $this->tanggal,
                    'total'               => $this->totalFaktur,
                    'status_pembayaran'   => $this->status_pembayaran,
                    'tanggal_jatuh_tempo' => $this->status_pembayaran === 'belum_lunas' ? $this->tanggal_jatuh_tempo : null,
                ]);

                foreach ($this->items as $row) {
                    $kemasan = KemasanBarang::with('barang')->findOrFail($row['id_kemasan']);

                    // Konversi jumlah kemasan (misal: Dus) ke satuan dasar (Pcs)
                    $qtySatuanDasar = $row['qty'] * $kemasan->isi_per_kemasan;
                    $hargaBeliSatuanDasar = $row['harga_beli_kemasan'] / $kemasan->isi_per_kemasan;
                    $subtotal = $row['qty'] * $row['harga_beli_kemasan'];

                    // 2. Simpan Detail Pembelian (dalam satuan dasar sesuai ERD 1)
                    DetailPembelian::create([
                        'id_pembelian' => $pembelian->id_pembelian,
                        'id_barang'    => $kemasan->id_barang,
                        'qty'          => $qtySatuanDasar,
                        'harga_beli'   => $hargaBeliSatuanDasar,
                        'subtotal'     => $subtotal,
                    ]);

                    // 3. Perbarui HPP terbaru pada master barang
                    $kemasan->barang->update([
                        'hpp' => $hargaBeliSatuanDasar,
                    ]);

                    // 4. Tambah stok & catat ke mutasi_stok via StokService (FR-09)
                    $stokService->tambahStok(
                        $kemasan->id_barang,
                        $qtySatuanDasar,
                        'pembelian',
                        $pembelian->id_pembelian,
                        Auth::id()
                    );
                }
            });

            session()->flash('success', 'Faktur pembelian berhasil disimpan & stok gudang otomatis bertambah!');
            return redirect()->route('gudang.pembelian.index');

        } catch (Exception $e) {
            session()->flash('error_pembelian', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.gudang.form-pembelian', [
            'pemasoks' => Pemasok::where('status_aktif', true)->get(),
            'kemasans' => KemasanBarang::with('barang')->whereHas('barang', fn($q) => $q->where('status_aktif', true))->get(),
        ]);
    }
}
