<x-app-layout>
    @section('title', 'Tambah Barang Baru')

    <form action="{{ route('owner.barang.store') }}" method="POST" class="max-w-4xl space-y-6">
        @csrf

        <!-- Bagian 1: Informasi Dasar Barang -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4 border-b pb-2">1. Informasi Dasar & Stok Satuan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Kode SKU *</label>
                    <input type="text" name="kode_sku" value="{{ old('kode_sku') }}" placeholder="Contoh: SKU-KOP-001" required class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Kategori *</label>
                    <select name="id_kategori" required class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id_kategori }}" {{ old('id_kategori') == $kat->id_kategori ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium text-slate-700 mb-1">Nama Barang *</label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" placeholder="Contoh: Kopi Kapal Api Special Mix 24gr" required class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Satuan Dasar *</label>
                    <input type="text" name="satuan" value="{{ old('satuan') }}" placeholder="pcs / sachet / botol / kg" required class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Barcode Kemasan Satuan (Scan USB)</label>
                    <input type="text" name="barcode_satuan" value="{{ old('barcode_satuan') }}" placeholder="Scan atau ketik kode barcode..." class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Harga Pokok Pembelian / HPP (Rp) *</label>
                    <input type="number" name="hpp" value="{{ old('hpp') }}" required class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Harga Jual Eceran Satuan (Rp) *</label>
                    <input type="number" name="harga_jual" value="{{ old('harga_jual') }}" required class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Stok Awal (Satuan Dasar) *</label>
                    <input type="number" name="stok_saat_ini" value="{{ old('stok_saat_ini', 0) }}" required class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Batas Stok Minimum (Reorder Point) *</label>
                    <input type="number" name="stok_minimum" value="{{ old('stok_minimum', 10) }}" required class="w-full rounded-lg border-slate-300">
                </div>
            </div>
        </div>

        <!-- Bagian 2: Kemasan Grosir (Dus / Pak) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-bold text-slate-800 mb-1">2. Kemasan Grosir / Dus (Opsional)</h2>
            <p class="text-xs text-slate-500 mb-4 border-b pb-2">Isi jika barang ini juga dijual dalam kemasan dus/renceng/pak.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Nama Kemasan (Misal: Dus Isi 120)</label>
                    <input type="text" name="nama_kemasan_2" value="{{ old('nama_kemasan_2') }}" class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Isi Konversi ke Satuan Dasar</label>
                    <input type="number" name="isi_kemasan_2" value="{{ old('isi_kemasan_2') }}" placeholder="Contoh: 120" class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Harga Jual per Kemasan Ini (Rp)</label>
                    <input type="number" name="harga_kemasan_2" value="{{ old('harga_kemasan_2') }}" class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Barcode Dus / Pak</label>
                    <input type="text" name="barcode_kemasan_2" value="{{ old('barcode_kemasan_2') }}" class="w-full rounded-lg border-slate-300">
                </div>
            </div>
        </div>

        <!-- Bagian 3: Aturan Harga Grosir Bertingkat (FR-10) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-bold text-slate-800 mb-1">3. Aturan Diskon Kuantitas / Grosir Satuan (Opsional)</h2>
            <p class="text-xs text-slate-500 mb-4 border-b pb-2">Harga satuan otomatis turun di kasir jika pelanggan membeli minimal jumlah tertentu.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Minimal Qty Beli (Satuan Dasar)</label>
                    <input type="number" name="grosir_qty_min" value="{{ old('grosir_qty_min') }}" placeholder="Contoh: 10" class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Harga Grosir per Satuan (Rp)</label>
                    <input type="number" name="grosir_harga" value="{{ old('grosir_harga') }}" placeholder="Harus lebih murah dari harga eceran" class="w-full rounded-lg border-slate-300">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('owner.barang.index') }}" class="px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Simpan Data Barang</button>
        </div>
    </form>
</x-app-layout>
