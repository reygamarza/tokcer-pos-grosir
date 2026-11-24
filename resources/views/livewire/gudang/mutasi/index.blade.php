<x-app-layout>
    @section('title', 'Kartu Riwayat Mutasi Stok')

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-800">Log Pergerakan Stok Real-Time (FR-09 & NFR-08)</h2>
                <p class="text-xs text-slate-500">Rekam jejak otomatis setiap barang masuk dan keluar dalam satuan dasar.</p>
            </div>
            <div class="flex gap-2 text-xs">
                <a href="{{ route('gudang.mutasi.index') }}" class="px-3 py-1.5 rounded-lg border {{ !request('arah') ? 'bg-slate-800 text-white' : 'bg-white text-slate-700' }}">Semua</a>
                <a href="{{ route('gudang.mutasi.index', ['arah' => 'masuk']) }}" class="px-3 py-1.5 rounded-lg border {{ request('arah') === 'masuk' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700' }}">Stok Masuk (+)</a>
                <a href="{{ route('gudang.mutasi.index', ['arah' => 'keluar']) }}" class="px-3 py-1.5 rounded-lg border {{ request('arah') === 'keluar' ? 'bg-rose-600 text-white' : 'bg-white text-slate-700' }}">Stok Keluar (-)</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th class="py-3 px-4 font-semibold">Waktu</th>
                        <th class="py-3 px-4 font-semibold">Barang</th>
                        <th class="py-3 px-4 font-semibold">Sumber Transaksi</th>
                        <th class="py-3 px-4 font-semibold text-center">Perubahan</th>
                        <th class="py-3 px-4 font-semibold text-center">Stok Sebelum → Sesudah</th>
                        <th class="py-3 px-4 font-semibold">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($mutasis as $m)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 text-xs text-slate-600">{{ \Carbon\Carbon::parse($m->waktu)->format('d/m/Y H:i:s') }}</td>
                        <td class="py-3 px-4 font-semibold text-slate-800">{{ $m->barang->nama_barang }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 text-xs rounded bg-slate-100 text-slate-700 uppercase font-medium">
                                {{ str_replace('_', ' ', $m->jenis_transaksi) }} #{{ $m->id_referensi }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-bold {{ $m->arah === 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $m->arah === 'masuk' ? '+' : '-' }}{{ $m->qty }} {{ $m->barang->satuan }}
                        </td>
                        <td class="py-3 px-4 text-center text-xs font-mono">
                            {{ $m->stok_sebelum }} → <span class="font-bold text-slate-800">{{ $m->stok_sesudah }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600">{{ $m->pengguna->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada catatan mutasi stok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $mutasis->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
