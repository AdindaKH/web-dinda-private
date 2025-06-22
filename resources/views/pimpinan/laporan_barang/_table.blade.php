<table class="table table-bordered table-striped">
    <thead class="table-dark">
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
        @foreach($barangs as $i => $barang)
            @php
                $kode = $barang->produk->kode ?? $barang->pendukung->kode ?? '-';
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $kode }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->exp ? \Carbon\Carbon::parse($barang->exp)->format('d-m-Y') : '-' }}</td>
                <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                <td>{{ $barang->qty ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>