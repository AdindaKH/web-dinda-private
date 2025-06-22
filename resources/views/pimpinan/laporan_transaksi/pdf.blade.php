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

    <h2>Laporan Transaksi Keuangan</h2>

    <div class="info">
        <p><strong>Periode:</strong> {{ $tanggal_mulai ?? '-' }} s/d {{ $tanggal_akhir ?? '-' }}</p>
        <p><strong>Filter Kata Kunci:</strong> {{ $q ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>Kode Transaksi</th>
                <th>Kode Barang</th>
                <th>Nama Mitra</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row['waktu'])->format('d-m-Y H:i') }}</td>
                <td>{{ $row['kode_transaksi'] }}</td>
                <td>{{ $row['kode_barang'] }}</td>
                <td>{{ $row['supplier'] }}</td>
                <td>{{ $row['nama_barang'] }}</td>
                <td>{{ $row['qty'] }}</td>
                <td>Rp {{ number_format($row['masuk'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($row['keluar'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd">
        <p>Mengetahui,</p>
        <p><strong>Poniman</strong></p>
        <p style="text-decoration: underline;">....................................</p>
    </div>

</body>
</html>
