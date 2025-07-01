<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji Kloter {{ $kloter->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 2.54cm; /* standar Word */
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
            line-height: 3;
        }

        .kop h2 {
            margin: 0;
            font-size: 16pt;
            line-height: 3; /* 3 spasi untuk judul */
        }

        .kop p {
            margin: 0;
            font-size: 12pt;
            line-height: 1.5;
        }

        hr {
            border: 1px solid black;
            margin-top: 10px;
        }

        h3 {
            text-align: center;
            margin: 10px 0;
            font-size: 14pt;
            line-height: 1.5;
        }

        .info p {
            margin: 0;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 6px;
            text-align: left;
            line-height: 1;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature div {
            width: 45%;
            line-height: 1;
        }

        .signature p {
            margin: 0;
            line-height: 1;
        }

        table.signature-table, .signature-table td {
            border: none !important;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @foreach ($slipData as $slip)
    <div class="container">
        <div class="kop">
            <h2>UD. DNL PUTRA</h2>
            <p>KP.Nyamplong, Desa Sumberanyar, Kecamatan Banyuputih, Kabupaten Situbondo</p>
            <p>Telp: 0812-3913-9713 | Email: info@ud-dnlputra.com</p>
            <hr>
        </div>

        <h3>SLIP GAJI KARYAWAN<br>Periode: {{ $tanggalMulai->format('d M Y') }} - {{ $tanggalAkhir->format('d M Y') }}</h3>

        <table style="margin: 15px 0 15px 0; width: auto; border: none;">
            <tr>
            <td style="border: none; padding: 2px 8px 2px 0;">Nama Karyawan</td>
            <td style="border: none; padding: 2px 8px;">:</td>
            <td style="border: none; padding: 2px 0;"><strong>{{ $slip['karyawan']->nama }}</strong></td>
            </tr>
            <tr>
            <td style="border: none; padding: 2px 8px 2px 0;">Jenis Kelamin</td>
            <td style="border: none; padding: 2px 8px;">:</td>
            <td style="border: none; padding: 2px 0;"><strong>{{ $slip['karyawan']->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</strong></td>
            </tr>
            <tr>
            <td style="border: none; padding: 2px 8px 2px 0;">Kloter</td>
            <td style="border: none; padding: 2px 8px;">:</td>
            <td style="border: none; padding: 2px 0;"><strong>{{ $kloter->id }}</strong></td>
            </tr>
            <tr>
            <td style="border: none; padding: 2px 8px 2px 0;">Tanggal Cetak</td>
            <td style="border: none; padding: 2px 8px;">:</td>
            <td style="border: none; padding: 2px 0;"><strong>{{ $tanggalCetak }}</strong></td>
            </tr>
        </table>

        <table>
            <thead>
                <tr><th>Tanggal</th><th>Jam Kerja</th></tr>
            </thead>
            <tbody>
                @foreach ($slip['jam_per_tanggal'] as $tanggal => $jam)
                <tr>
                    <td>{{ $tanggal }}</td>
                    <td>{{ floor($jam) }} jam {{ round(($jam - floor($jam)) * 60) }} menit</td>
                </tr>
                @endforeach
                <tr>
                    <th>Total</th>
                    <th>{{ floor($slip['total_jam']) }} jam {{ round(($slip['total_jam'] - floor($slip['total_jam'])) * 60) }} menit</th>
                </tr>
            </tbody>
        </table>

        <table>
            <tr><td>Gaji per Jam</td><td>Rp{{ number_format($slip['gaji_per_jam'], 0, ',', '.') }}</td></tr>
            <tr><td>Total Jam Kerja</td><td>{{ floor($slip['total_jam']) }} jam {{ round(($slip['total_jam'] - floor($slip['total_jam'])) * 60) }} menit</td></tr>
            <tr><td><strong>Total Gaji</strong></td><td><strong>Rp{{ number_format($slip['total_gaji'], 0, ',', '.') }}</strong></td></tr>
        </table>

        <table class="signature-table" style="width: 100%; margin: 30px 0 0 0; border: none;">
            <tr>
            <td style="width: 50%; text-align: left; border: none;">
                Mengetahui,<br><br><br><br><br>
                <strong>Poniman</strong>
            </td>
            <td style="width: 50%; text-align: right; border: none;">
                Diterima oleh,<br><br><br><br><br>
                <strong>{{ $slip['karyawan']->nama }}</strong>
            </td>
            </tr>
        </table>
    </div>
    <div class="page-break"></div>
    @endforeach
</body>
</html>
