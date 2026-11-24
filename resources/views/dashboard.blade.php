<x-app-layout>
    @section('title', 'Dashboard Ringkasan Operasional')

    @php
        $totalBarang = \App\Models\Barang::where('status_aktif', true)->count();
        $stokMenipis = \App\Models\Barang::whereColumn('stok_saat_ini', '<=', 'stok_minimum')->where('status_aktif', true)->count();
        $transaksiHariIni = \App\Models\Penjualan::whereDate('tanggal', today())->where('status', 'selesai')->count();
        $omzetHariIni = \App\Models\Penjualan::whereDate('tanggal', today())->where('status', 'selesai')->sum('total');
    @endphp

    <!-- Kartu Statistik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Total Produk Aktif</p>
            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $totalBarang }} <span class="text-sm font-normal text-slate-500">item</span></p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Peringatan Stok Minimum</p>
            <p class="mt-2 text-2xl font-bold {{ $stokMenipis > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                {{ $stokMenipis }} <span class="text-sm font-normal text-slate-500">item</span>
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Transaksi Kasir Hari Ini</p>
            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $transaksiHariIni }} <span class="text-sm font-normal text-slate-500">nota</span></p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Omzet Penjualan Hari Ini</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Akses Cepat -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-800 mb-4">Akses Cepat Operasional</h2>
        <div class="flex flex-wrap gap-3 text-sm">
            @role('Owner')
            <a href="{{ route('owner.barang.create') }}" class="px-4 py-2.5 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700">
                + Tambah Barang & Grosir
            </a>
            <a href="{{ route('owner.barang.index') }}" class="px-4 py-2.5 bg-slate-800 text-white font-semibold rounded-lg hover:bg-slate-700">
                Lihat Daftar Barang
            </a>
            @endrole
        </div>
    </div>
</x-app-layout>
