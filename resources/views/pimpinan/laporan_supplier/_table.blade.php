<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
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
            @forelse($suppliers as $i => $supplier)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $supplier->nama }}</td>
                <td>{{ $supplier->pemasok ? 'Pemasok' : 'Konsumen' }}</td>
                <td>{{ $supplier->alamat }}</td>
                <td>{{ $supplier->no_tlp }}</td>
                <td>{{ $supplier->no_rekening ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
