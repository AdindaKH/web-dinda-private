<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #000;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
        }
        .ttd {
            width: 100%;
            margin-top: 60px;
            text-align: right;
        }
        .ttd p {
            margin-bottom: 70px;
        }
    </style>
</head>
<body>
    <h3 style="text-align: center;">Laporan Barang</h3>
    <p>Filter: {{ $filter ?? '-' }}</p>
    <p>Nama Barang: {{ $nama ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Exp</th>
                <th>Harga</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $i => $b)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $b->produk->kode ?? $b->pendukung->kode ?? '-' }}</td>
                    <td>{{ $b->nama_barang }}</td>
                    <td>{{ optional($b->exp)->format('d-m-Y') }}</td>
                    <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                    <td>{{ $b->qty }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br><br>
    <div style="text-align: right;">
        <p>Mengetahui,</p>
        <p style="margin-top: 60px;">Poniman</p>
        <p>....................................</p>
    </div>
</body>
</html>