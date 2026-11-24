<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\KemasanBarang;
use App\Models\AturanHargaGrosir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with(['kategori', 'kemasan', 'aturanGrosir']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_sku', 'like', "%{$search}%");
            });
        }

        if ($request->filter === 'menipis') {
            $query->whereColumn('stok_saat_ini', '<=', 'stok_minimum');
        }

        $barangs = $query->latest('id_barang')->paginate(10);
        return view('owner.barang.index', compact('barangs'));
    }

    public function create()
    {
        $kategoris = KategoriBarang::all();
        return view('owner.barang.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori'      => 'required|exists:kategori_barang,id_kategori',
            'kode_sku'         => 'required|string|max:50|unique:barang,kode_sku',
            'nama_barang'      => 'required|string|max:150',
            'satuan'           => 'required|string|max:30',
            'hpp'              => 'required|numeric|min:0',
            'harga_jual'       => 'required|numeric|min:0',
            'stok_minimum'     => 'required|integer|min:0',
            'stok_saat_ini'    => 'required|integer|min:0',
            'barcode_satuan'   => 'nullable|string|max:50|unique:kemasan_barang,kode_barcode',
            // Opsional kemasan dus/pak
            'nama_kemasan_2'   => 'nullable|string|max:50',
            'isi_kemasan_2'    => 'nullable|integer|min:2',
            'harga_kemasan_2'  => 'nullable|numeric|min:0',
            'barcode_kemasan_2'=> 'nullable|string|max:50|unique:kemasan_barang,kode_barcode',
            // Opsional aturan grosir satuan
            'grosir_qty_min'   => 'nullable|integer|min:2',
            'grosir_harga'     => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan Master Barang (Satuan Dasar)
            $barang = Barang::create([
                'id_kategori'   => $request->id_kategori,
                'kode_sku'      => $request->kode_sku,
                'nama_barang'   => $request->nama_barang,
                'satuan'        => $request->satuan,
                'hpp'           => $request->hpp,
                'harga_jual'    => $request->harga_jual,
                'stok_minimum'  => $request->stok_minimum,
                'stok_saat_ini' => $request->stok_saat_ini,
                'status_aktif'  => true,
            ]);

            // 2. Otomatis Buat Kemasan 1 (Satuan Dasar)
            $kemasanDasar = KemasanBarang::create([
                'id_barang'       => $barang->id_barang,
                'nama_kemasan'    => ucfirst($request->satuan) . ' (Satuan)',
                'kode_barcode'    => $request->barcode_satuan,
                'isi_per_kemasan' => 1,
                'harga_jual'      => $request->harga_jual,
            ]);

            // 3. Jika Diisi, Buat Kemasan 2 (Misal: Dus / Pak)
            if ($request->filled('nama_kemasan_2') && $request->filled('isi_kemasan_2') && $request->filled('harga_kemasan_2')) {
                KemasanBarang::create([
                    'id_barang'       => $barang->id_barang,
                    'nama_kemasan'    => $request->nama_kemasan_2,
                    'kode_barcode'    => $request->barcode_kemasan_2,
                    'isi_per_kemasan' => $request->isi_kemasan_2,
                    'harga_jual'      => $request->harga_kemasan_2,
                ]);
            }

            // 4. Jika Diisi, Buat Aturan Harga Grosir Bertingkat (FR-10)
            if ($request->filled('grosir_qty_min') && $request->filled('grosir_harga')) {
                AturanHargaGrosir::create([
                    'id_barang'    => $barang->id_barang,
                    'id_kemasan'   => $kemasanDasar->id_kemasan,
                    'qty_minimum'  => $request->grosir_qty_min,
                    'harga_grosir' => $request->grosir_harga,
                    'status_aktif' => true,
                ]);
            }
        });

        return redirect()->route('owner.barang.index')->with('success', 'Data barang, kemasan, dan aturan grosir berhasil disimpan!');
    }

    public function destroy(Barang $barang)
    {
        // Sesuai FR-01: Menonaktifkan barang (soft-deactivation) agar riwayat transaksi lama tidak rusak
        $barang->update(['status_aktif' => !$barang->status_aktif]);
        return back()->with('success', 'Status aktif barang berhasil diperbarui.');
    }
}
