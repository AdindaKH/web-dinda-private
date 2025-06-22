<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Waktu</th>
            <th>Kode Transaksi</th>
            <th>Kode Barang</th>
            <th>Mitra</th>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Masuk</th>
            <th>Keluar</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $trx)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($trx['waktu'])->format('d-m-Y H:i') }}</td>
            <td>{{ $trx['kode_transaksi'] }}</td>
            <td>{{ $trx['kode_barang'] }}</td>
            <td>{{ $trx['supplier'] }}</td>
            <td>{{ $trx['nama_barang'] }}</td>
            <td>{{ $trx['qty'] }}</td>
            <td>Rp {{ number_format($trx['masuk'], 0, ',', '.') }}</td>
            <td>Rp {{ number_format($trx['keluar'], 0, ',', '.') }}</td>
            <td>Rp {{ number_format($trx['total'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>