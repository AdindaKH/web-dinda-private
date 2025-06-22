<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
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
        @forelse ($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['karyawan']->nama }}</td>
                <td>{{ $item['karyawan']->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                <td>{{ $item['karyawan']->no_telepon }}</td>
                <td>{{ $item['total_jam_kerja'] }} Jam</td>
                <td>
                    <ul class="mb-0 ps-3">
                        @foreach ($item['gaji_per_kloter'] as $gpk)
                            <li>
                                Kloter ID {{ $gpk['kloter_id'] }}:
                                Rp {{ number_format($gpk['gaji'], 0, ',', '.') }} 
                                ({{ $gpk['total_jam'] }} jam)
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td><strong>Rp {{ number_format($item['total_gaji'], 0, ',', '.') }}</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data karyawan</td>
            </tr>
        @endforelse
    </tbody>
</table>
