<div>
    @section('title', 'Catat Pembelian / Kulakan Stok Masuk')

    @if(session('error_pembelian'))
        <div class="mb-4 p-3 rounded-lg bg-rose-100 border border-rose-300 text-rose-800 text-sm">
            ⚠️ {{ session('error_pembelian') }}
        </div>
    @endif

    <form wire:submit.prevent="simpanPembelian" class="space-y-6">
        <!-- Header Faktur Pemasok -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4 border-b pb-2">1. Informasi Faktur Pemasok (FR-04)</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">No. Faktur / Nota Supplier *</label>
                    <input type="text" wire:model="no_faktur" class="w-full rounded-lg border-slate-300">
                    @error('no_faktur') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Pemasok (Supplier) *</label>
                    <select wire:model="id_pemasok" class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach($pemasoks as $p)
                            <option value="{{ $p->id_pemasok }}">{{ $p->nama_pemasok }}</option>
                        @endforeach
                    </select>
                    @error('id_pemasok') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Tanggal Pembelian *</label>
                    <input type="date" wire:model="tanggal" class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Status Pembayaran *</label>
                    <select wire:model.live="status_pembayaran" class="w-full rounded-lg border-slate-300">
                        <option value="lunas">Tunai / Lunas</option>
                        <option value="belum_lunas">Kredit / Tempo (Utang Usaha)</option>
                    </select>
                </div>
                @if($status_pembayaran === 'belum_lunas')
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Tanggal Jatuh Tempo *</label>
                    <input type="date" wire:model="tanggal_jatuh_tempo" class="w-full rounded-lg border-slate-300">
                    @error('tanggal_jatuh_tempo') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>
        </div>

        <!-- Rincian Barang yang Dibeli -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <div>
                    <h2 class="text-base font-bold text-slate-800">2. Daftar Barang Masuk</h2>
                    <p class="text-xs text-slate-500">Pilih kemasan (Dus/Pcs), sistem otomatis mengonversi ke satuan dasar stok gudang.</p>
                </div>
                <button type="button" wire:click="tambahBaris" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700">
                    + Tambah Baris Barang
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <th class="py-2.5 px-3 font-semibold">Barang & Kemasan Kulakan</th>
                            <th class="py-2.5 px-3 font-semibold w-28 text-center">Qty Beli</th>
                            <th class="py-2.5 px-3 font-semibold w-44 text-right">Harga Beli / Kemasan</th>
                            <th class="py-2.5 px-3 font-semibold w-44 text-right">Subtotal</th>
                            <th class="py-2.5 px-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($items as $index => $item)
                        <tr>
                            <td class="py-2.5 px-3">
                                <select wire:model.live="items.{{ $index }}.id_kemasan" class="w-full rounded-lg border-slate-300 text-sm">
                                    <option value="">-- Pilih Barang & Kemasan --</option>
                                    @foreach($kemasans as $kms)
                                        <option value="{{ $kms->id_kemasan }}">
                                            {{ $kms->barang->nama_barang }} — [{{ $kms->nama_kemasan }} | Isi {{ $kms->isi_per_kemasan }} {{ $kms->barang->satuan }}]
                                        </option>
                                    @endforeach
                                </select>
                                @error("items.{$index}.id_kemasan") <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="number" min="1" wire:model.live="items.{{ $index }}.qty" class="w-full text-center rounded-lg border-slate-300 text-sm">
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="number" min="0" wire:model.live="items.{{ $index }}.harga_beli_kemasan" class="w-full text-right rounded-lg border-slate-300 text-sm">
                            </td>
                            <td class="py-2.5 px-3 text-right font-semibold text-slate-800">
                                Rp {{ number_format((float)($item['qty'] ?? 0) * (float)($item['harga_beli_kemasan'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if(count($items) > 1)
                                    <button type="button" wire:click="hapusBaris({{ $index }})" class="text-rose-500 hover:text-rose-700 font-bold">✕</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-200">
                            <td colspan="3" class="py-3 px-3 text-right font-bold text-slate-700">TOTAL FAKTUR PEMBELIAN</td>
                            <td class="py-3 px-3 text-right font-extrabold text-lg text-emerald-700">
                                Rp {{ number_format($this->totalFaktur, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('gudang.pembelian.index') }}" class="px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                Simpan Pembelian & Tambah Stok
            </button>
        </div>
    </form>
</div>
