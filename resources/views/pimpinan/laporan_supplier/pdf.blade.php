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
    <h3 style="text-align: center;">Laporan Data Mitra Bisnis</h3>
    <p>Kategori: {{ $kategori ?? 'Semua' }}</p>
    <p>Kata Kunci: {{ $keyword ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Alamat</th>
                <th>No Telepon</th>
                <th>No Rekening</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $i => $supplier)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $supplier->nama }}</td>
                    <td>{{ $supplier->pemasok ? 'Pemasok' : 'Konsumen' }}</td>
                    <td>{{ $supplier->alamat }}</td>
                    <td>{{ $supplier->no_tlp }}</td>
                    <td>{{ $supplier->no_rekening ?? '-' }}</td>
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