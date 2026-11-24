<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $penjualan->no_transaksi }}</title>
    <style>
        * { font-family: 'Courier New', Courier, monospace; box-sizing: border-box; }
        body { width: 80mm; margin: 0 auto; padding: 10px; font-size: 12px; color: #000; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
        @media print {
            .no-print { display: none; }
            body { width: 100%; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 12px; text-align: center;">
        <a href="{{ route('kasir.pos') }}" style="padding: 6px 12px; background: #059669; color: #fff; text-decoration: none; border-radius: 4px;">← Kembali ke Kasir</a>
        <button onclick="window.print()" style="padding: 6px 12px; cursor: pointer;">Cetak Ulang</button>
    </div>

    <div class="center">
        <div class="bold" style="font-size: 15px;">TOKO KELONTONG & GROSIR TOKCER</div>
        <div>Toko Kang Aden - Bandung</div>
    </div>

    <div class="line"></div>
    <table>
        <tr><td>No Nota</td><td class="right">{{ $penjualan->no_transaksi }}</td></tr>
        <tr><td>Tanggal</td><td class="right">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Kasir</td><td class="right">{{ $penjualan->kasir->name }}</td></tr>
    </table>
    <div class="line"></div>

    <table>
        @foreach($penjualan->detail as $det)
        <tr>
            <td colspan="2" class="bold">
                {{ $det->kemasan->barang->nama_barang }} ({{ $det->kemasan->nama_kemasan }})
                @if($det->tipe_harga === 'grosir') [GROSIR] @endif
            </td>
        </tr>
        <tr>
            <td>{{ $det->qty }} x {{ number_format($det->harga_satuan, 0, ',', '.') }}</td>
            <td class="right">{{ number_format($det->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>
    <table>
        <tr><td>Subtotal</td><td class="right">{{ number_format($penjualan->subtotal, 0, ',', '.') }}</td></tr>
        @if($penjualan->diskon > 0)
        <tr><td>Diskon</td><td class="right">-{{ number_format($penjualan->diskon, 0, ',', '.') }}</td></tr>
        @endif
        <tr class="bold" style="font-size: 14px;"><td>TOTAL</td><td class="right">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td></tr>
        <tr><td>Bayar ({{ strtoupper($penjualan->metode_pembayaran) }})</td><td class="right">{{ number_format($penjualan->jumlah_bayar, 0, ',', '.') }}</td></tr>
        <tr><td>Kembali</td><td class="right">{{ number_format($penjualan->kembalian, 0, ',', '.') }}</td></tr>
    </table>
    <div class="line"></div>

    <div class="center" style="margin-top: 8px;">
        Terima kasih telah berbelanja di TokCer!<br>
        Barang yang sudah dibeli tidak dapat ditukar kecuali perjanjian.
    </div>
</body>
</html>
