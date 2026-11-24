<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\MutasiStok;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function indexPembelian()
    {
        $pembelians = Pembelian::with(['pemasok', 'pengguna', 'detail.barang'])
            ->latest('id_pembelian')
            ->paginate(10);

        return view('gudang.pembelian.index', compact('pembelians'));
    }

    public function indexMutasi(Request $request)
    {
        $mutasis = MutasiStok::with(['barang', 'pengguna'])
            ->when($request->arah, fn($q) => $q->where('arah', $request->arah))
            ->latest('id_mutasi')
            ->paginate(15);

        return view('gudang.mutasi.index', compact('mutasis'));
    }
}
