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
    <h3 style="text-align: center;">Laporan Gaji Karyawan</h3>
    <p>Filter Waktu: {{ $filter ?? '-' }}</p>
    <p>Nama: {{ $nama ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pekerja</th>
                <th>Jenis Kelamin</th>
                <th>No Telepon</th>
                <th>Total Jam Kerja</th>
                <th>Gaji per Kloter</th>
                <th>Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item['karyawan']->nama }}</td>
                    <td>{{ $item['karyawan']->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ $item['karyawan']->no_telepon }}</td>
                    <td>{{ $item['total_jam_kerja'] }} Jam</td>
                    <td>
                        @foreach($item['gaji_per_kloter'] as $gaji)
                            Kloter ID {{ $gaji['kloter_id'] }}: Rp {{ number_format($gaji['gaji'], 0, ',', '.') }} ({{ $gaji['total_jam'] }} jam)<br>
                        @endforeach
                    </td>
                    <td>Rp {{ number_format($item['total_gaji'], 0, ',', '.') }}</td>
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