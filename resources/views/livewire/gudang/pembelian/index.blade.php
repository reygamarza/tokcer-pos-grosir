<x-app-layout>
    @section('title', 'Riwayat Pembelian & Penerimaan Barang')

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Faktur Pembelian Pemasok</h2>
                <p class="text-xs text-slate-500">Setiap faktur otomatis menambah stok satuan dasar gudang.</p>
            </div>
            <a href="{{ route('gudang.pembelian.create') }}" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                + Catat Pembelian Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th class="py-3 px-4 font-semibold">No. Faktur & Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Pemasok</th>
                        <th class="py-3 px-4 font-semibold">Rincian Barang Masuk</th>
                        <th class="py-3 px-4 font-semibold text-right">Total Faktur</th>
                        <th class="py-3 px-4 font-semibold text-center">Status Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($pembelians as $pb)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4">
                            <div class="font-mono font-semibold text-slate-800">{{ $pb->no_faktur }}</div>
                            <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($pb->tanggal)->format('d M Y') }} • Oleh: {{ $pb->pengguna->name }}</div>
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $pb->pemasok->nama_pemasok }}</td>
                        <td class="py-3 px-4 space-y-1">
                            @foreach($pb->detail as $det)
                                <div class="text-xs text-slate-700">
                                    • {{ $det->barang->nama_barang }}: <span class="font-bold text-emerald-700">+{{ $det->qty }} {{ $det->barang->satuan }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-slate-800">
                            Rp {{ number_format($pb->total, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($pb->status_pembayaran === 'lunas')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Lunas</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                    Tempo (JT: {{ \Carbon\Carbon::parse($pb->tanggal_jatuh_tempo)->format('d/m/Y') }})
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">Belum ada riwayat pembelian barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pembelians->links() }}</div>
    </div>
</x-app-layout>
