<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TokCer POS & Grosir') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
            <div class="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-wider text-emerald-400">
                    TOKCER <span class="text-xs font-normal text-slate-400 block">Sistem Kelontong & Grosir</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">✕</button>
            </div>

            <!-- Info User & Role -->
            <div class="px-6 py-4 bg-slate-800/50 border-b border-slate-800">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded bg-emerald-500/20 text-emerald-300">
                    {{ Auth::user()->getRoleNames()->first() ?? 'Staf' }}
                </span>
            </div>

            <!-- Navigasi Dinamis Berdasarkan Role (FR-23) -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                    Dashboard Utama
                </a>

                @hasanyrole('Owner|Kasir')
                <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Transaksi</div>
                <a href="{{ route('kasir.pos') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('kasir.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                    POS Kasir Digital
                </a>
                @endhasanyrole

                @role('Owner')
                <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Master Data</div>
                <a href="{{ route('owner.barang.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('owner.barang.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                    Data Barang & Grosir
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800">
                    Kategori & Pemasok
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800">
                    Pengguna & Audit Trail
                </a>
                @endrole

                @hasanyrole('Owner|Gudang')
               <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Gudang & Stok</div>
                <a href="{{ route('gudang.pembelian.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('gudang.pembelian.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                    Pembelian & Penerimaan
                </a>
                <a href="{{ route('gudang.mutasi.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('gudang.mutasi.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                    Kartu Mutasi Stok
                </a>
                @endhasanyrole

                @hasanyrole('Owner|Finance')
                <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Keuangan</div>
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800">
                    Utang, Piutang & Kas
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800">
                    Laporan Keuangan
                </a>
                @endhasanyrole
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 text-left text-sm text-rose-400 hover:bg-slate-800 rounded-lg">
                        Keluar Sistem
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-slate-600 hover:bg-slate-100">
                    ☰
                </button>
                <div class="font-semibold text-slate-700">
                    @yield('title', 'Sistem Informasi TokCer')
                </div>

                <!-- Indikator Notifikasi Stok Minimum Otomatis (FR-16) -->
                @php
                    $stokMenipisCount = \App\Models\Barang::whereColumn('stok_saat_ini', '<=', 'stok_minimum')->where('status_aktif', true)->count();
                @endphp
                <div class="flex items-center gap-4">
                    @if($stokMenipisCount > 0)
                        <a href="{{ route('owner.barang.index', ['filter' => 'menipis']) }}" class="px-3 py-1.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                            ⚠️ {{ $stokMenipisCount }} Barang Stok Menipis
                        </a>
                    @else
                        <span class="px-3 py-1.5 text-xs font-medium rounded-full bg-emerald-50 text-emerald-700">
                            ✓ Stok Aman
                        </span>
                    @endif
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
