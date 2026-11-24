<x-app-layout>
    @section('title', 'Master Data Barang & Harga Grosir')

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <form method="GET" action="{{ route('owner.barang.index') }}" class="flex gap-2 w-full sm:w-96">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang atau SKU..." class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700">Cari</button>
            </form>

            <a href="{{ route('owner.barang.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                + Tambah Barang Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600">
                        <th class="py-3 px-4 font-semibold">SKU & Barang</th>
                        <th class="py-3 px-4 font-semibold">Kategori</th>
                        <th class="py-3 px-4 font-semibold">Kemasan & Harga Jual</th>
                        <th class="py-3 px-4 font-semibold">Aturan Grosir (FR-10)</th>
                        <th class="py-3 px-4 font-semibold">Stok</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($barangs as $item)
                    <tr class="hover:bg-slate-50 {{ !$item->status_aktif ? 'opacity-50 bg-slate-100' : '' }}">
                        <td class="py-3 px-4">
                            <span class="text-xs font-mono text-slate-500">{{ $item->kode_sku }}</span>
                            <div class="font-semibold text-slate-800">{{ $item->nama_barang }}</div>
                            <div class="text-xs text-slate-500">HPP: Rp {{ number_format($item->hpp, 0, ',', '.') }} / {{ $item->satuan }}</div>
                        </td>
                        <td class="py-3 px-4">{{ $item->kategori->nama_kategori }}</td>
                        <td class="py-3 px-4 space-y-1">
                            @foreach($item->kemasan as $kms)
                                <div class="text-xs">
                                    <span class="font-medium text-slate-700">{{ $kms->nama_kemasan }}:</span>
                                    <span class="text-emerald-700 font-semibold">Rp {{ number_format($kms->harga_jual, 0, ',', '.') }}</span>
                                    @if($kms->kode_barcode)
                                        <span class="text-slate-400">[{{ $kms->kode_barcode }}]</span>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 space-y-1">
                            @forelse($item->aturanGrosir as $grs)
                                <span class="inline-block px-2 py-0.5 text-xs rounded bg-blue-50 text-blue-700 border border-blue-200">
                                    Min {{ $grs->qty_minimum }} {{ $grs->kemasan->nama_kemasan ?? $item->satuan }} → Rp {{ number_format($grs->harga_grosir, 0, ',', '.') }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400">- Tidak ada -</span>
                            @endforelse
                        </td>
                        <td class="py-3 px-4">
                            @if($item->stok_saat_ini <= $item->stok_minimum)
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-700">
                                    {{ $item->stok_saat_ini }} {{ $item->satuan }} (Min: {{ $item->stok_minimum }})
                                </span>
                            @else
                                <span class="font-semibold text-slate-700">{{ $item->stok_saat_ini }} {{ $item->satuan }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <form action="{{ route('owner.barang.destroy', $item->id_barang) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs px-3 py-1.5 rounded border {{ $item->status_aktif ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                    {{ $item->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">Belum ada data barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $barangs->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
