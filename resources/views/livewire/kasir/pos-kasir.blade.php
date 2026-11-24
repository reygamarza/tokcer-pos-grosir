<div>
    @section('title', 'POS Kasir Digital')

    @if(session('error_kasir'))
        <div class="mb-4 p-3 rounded-lg bg-rose-100 border border-rose-300 text-rose-800 text-sm font-medium">
            ⚠️ {{ session('error_kasir') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KOLOM KIRI (2/3): Input Scan Barcode & Keranjang Belanja -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Kotak Scan Barcode / Cari Nama Barang -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 relative">
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">
                    Scan Barcode USB (Tekan Enter) atau Ketik Nama Barang
                </label>
                <div class="flex gap-2">
                    <input
                        type="text"
                        wire:model.live="search"
                        wire:keydown.enter.prevent="scanAtauPilihBarcode"
                        autofocus
                        placeholder="Arahkan barcode scanner ke sini atau ketik 'Indomie'..."
                        class="w-full rounded-lg border-slate-300 text-base focus:border-emerald-500 focus:ring-emerald-500"
                    >
                    <button type="button" wire:click="scanAtauPilihBarcode" class="px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-700">
                        Tambah
                    </button>
                </div>

                <!-- Dropdown Hasil Pencarian Nama Barang -->
                @if(!empty($hasilPencarian))
                    <div class="absolute left-4 right-4 mt-1 bg-white border border-slate-300 rounded-lg shadow-lg z-20 divide-y divide-slate-100">
                        @foreach($hasilPencarian as $kms)
                            <button
                                type="button"
                                wire:click="tambahKeKeranjang({{ $kms->id_kemasan }}); $set('search', '')"
                                class="w-full px-4 py-2.5 text-left hover:bg-emerald-50 flex items-center justify-between text-sm"
                            >
                                <div>
                                    <span class="font-semibold text-slate-800">{{ $kms->barang->nama_barang }}</span>
                                    <span class="ml-1 px-2 py-0.5 text-xs rounded bg-slate-100 text-slate-600">{{ $kms->nama_kemasan }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-emerald-700">Rp {{ number_format($kms->harga_jual, 0, ',', '.') }}</span>
                                    <span class="block text-xs text-slate-400">Stok: {{ $kms->barang->stok_saat_ini }} {{ $kms->barang->satuan }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tabel Keranjang Belanja -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                                <th class="py-3 px-4 font-semibold">Barang & Kemasan</th>
                                <th class="py-3 px-4 font-semibold text-center w-28">Qty</th>
                                <th class="py-3 px-4 font-semibold text-right">Harga Satuan</th>
                                <th class="py-3 px-4 font-semibold text-right">Subtotal</th>
                                <th class="py-3 px-4 text-center w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($cart as $idKemasan => $item)
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-800">{{ $item['nama_barang'] }}</div>
                                        <div class="text-xs text-slate-500">
                                            Kemasan: {{ $item['nama_kemasan'] }}
                                            @if($item['tipe_harga'] === 'grosir')
                                                <span class="ml-1 px-2 py-0.5 font-bold rounded bg-emerald-100 text-emerald-800">HARGA GROSIR</span>
                                            @else
                                                <span class="ml-1 px-2 py-0.5 rounded bg-slate-100 text-slate-600">Eceran</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input
                                            type="number"
                                            min="1"
                                            value="{{ $item['qty'] }}"
                                            wire:change="updateQty({{ $idKemasan }}, $event.target.value)"
                                            class="w-20 text-center rounded-lg border-slate-300 text-sm font-semibold"
                                        >
                                    </td>
                                    <td class="py-3 px-4 text-right font-medium">
                                        Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-800">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <button type="button" wire:click="hapusItem({{ $idKemasan }})" class="text-rose-500 hover:text-rose-700 font-bold">✕</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        Keranjang belanja kosong. Scan barcode atau cari barang di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (1/3): Ringkasan Pembayaran -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between h-fit space-y-5">
            <div class="space-y-4">
                <h2 class="text-base font-bold text-slate-800 border-b pb-2">Pembayaran Kasir</h2>

                <div class="flex justify-between text-sm text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-semibold">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Diskon Tambahan Nota (Rp)</label>
                    <input type="number" min="0" wire:model.live="diskonTransaksi" class="w-full rounded-lg border-slate-300 text-sm text-right">
                </div>

                <div class="p-4 rounded-xl bg-slate-900 text-white">
                    <span class="text-xs text-slate-400 uppercase">Total Tagihan</span>
                    <div class="text-3xl font-extrabold text-emerald-400 mt-1">
                        Rp {{ number_format($this->totalAkhir, 0, ',', '.') }}
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Metode Pembayaran</label>
                    <select wire:model.live="metodePembayaran" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="tunai">Tunai (Cash)</option>
                        <option value="non_tunai">Non-Tunai (QRIS / Transfer)</option>
                    </select>
                </div>

                @if($metodePembayaran === 'tunai')
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Uang Diterima (Rp)</label>
                        <input type="number" min="0" wire:model.live="jumlahBayar" placeholder="0" class="w-full rounded-lg border-slate-300 text-lg font-bold text-right">
                    </div>

                    <div class="flex justify-between items-center p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                        <span class="text-sm font-medium text-emerald-800">Kembalian</span>
                        <span class="text-lg font-bold text-emerald-700">Rp {{ number_format($this->kembalian, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <button
                type="button"
                wire:click="prosesBayar"
                class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm text-base transition"
            >
                Bayar & Cetak Struk (FR-08)
            </button>
        </div>
    </div>
</div>
