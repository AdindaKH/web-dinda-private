@extends('layouts.app_pimpinan')

@section('title', 'Dashboard Pimpinan')

@section('content')
<div class="container mt-4">
    <div class="title-box">
        <h3 class="fw-bold m-0">Dashboard</h3>
    </div>

    <div class="dashboard-container" style="height: 100%;">
    <div class="info-row">
        <div class="info-box text-start">
            <div class="fw-bold">Pendapatan</div>
            <div class="text-success fw-bold mt-1">Rp {{ number_format($keuangan['pendapatan'], 0, ',', '.') }}</div>
        </div>
        
        <div class="info-box">
            <div>Pengeluaran</div>
            <div class="text-danger mt-2">
                Rp {{ number_format($keuangan['pengeluaran'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="chart-card bg-yellow-50 p-4 rounded shadow-md mt-4" style="height:auto;">
        <div class="chart-c flex justify-between items-center mb-4">
            <h2 class="font-bold text-lg">
                Grafik Pendapatan <span class="text-dark">vs</span> Pengeluaran
                <span class="text-sm text-gray-600">({{ $bulanAktif }})</span>
            </h2>
    
            <form id="date-filter-form" class="flex items-center gap-2" style="padding-top: 10px">
                <input style="width: fit-content" type="date" name="start_date" id="start_date" 
                    value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}"
                    class="filter-info border px-2 py-1 rounded text-sm">
                <input style="width: fit-content" type="date" name="end_date" id="end_date" 
                    value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}"
                    class="filter-info border px-2 py-1 rounded text-sm">

                <select id="kloterFilter" name="kloter_id" class="border px-2 py-1 rounded text-sm">
                    <option value="">Pilih Kloter</option>
                    @foreach($kloters as $kloter)
                        <option value="{{ $kloter->id }}" 
                            data-start="{{ $kloter->tanggal_awal }}" 
                            data-end="{{ $kloter->tanggal_akhir }}"
                            {{ request('kloter_id') == $kloter->id ? 'selected' : '' }}>
                            Kloter {{ $kloter->kloter_id }} ({{ $kloter->tanggal_awal }} - {{ $kloter->tanggal_akhir }})
                        </option>
                    @endforeach
                </select>
            </form>

        </div>
    
        <div class="chart-card">
            <canvas id="financeChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const form = document.getElementById('date-filter-form');
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');

    [startInput, endInput].forEach(input => {
        input.addEventListener('change', () => {
            const startDate = startInput.value;
            const endDate = endInput.value;
            if (startDate && endDate && startDate <= endDate) {
                form.submit();
            }
        });
    });

    document.getElementById('kloterFilter').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const start = selected.getAttribute('data-start');
        const end = selected.getAttribute('data-end');

        if (start && end) {
            document.getElementById('start_date').value = start;
            document.getElementById('end_date').value = end;
        }

        document.getElementById('date-filter-form').submit();
    });

    const ctx = document.getElementById('financeChart').getContext('2d');
    const financeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [
                {
                    label: 'Pendapatan',
                    data: {!! json_encode($pendapatanBulanan) !!},
                    borderColor: 'blue',
                    backgroundColor: 'blue',
                    fill: false,
                    tension: 0.4, // Membuat garis lengkung
                    pointRadius: 4,
                    pointBackgroundColor: 'blue',
                    borderWidth: 3, // Garis lebih tebal
                    showLine: true, // Pastikan garis antar titik muncul
                    },
                    {
                        label: 'Pengeluaran',
                        data: [0, ...{!! json_encode($pengeluaranBulanan) !!}, 0], // Awali & akhiri dengan 0
                        borderColor: 'red',
                        backgroundColor: 'red',
                        fill: false,
                        tension: 0.4, // Membuat garis lengkung
                        pointRadius: 4,
                        pointBackgroundColor: 'red',
                        borderWidth: 4, // Garis lebih tebal
                        showLine: true // Pastikan garis antar titik muncul
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                elements: {
                    line: {
                        cubicInterpolationMode: 'monotone', // Garis lengkung halus
                    }
                },
                spanGaps: true, // Hubungkan titik meskipun ada data kosong
                scales: {
                    y: {
                        type: 'logarithmic',
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, ticks) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

    // Tambahkan titik 0 di awal dan akhir dataset untuk Pendapatan
    financeChart.data.datasets[0].data = [0, ...{!! json_encode($pendapatanBulanan) !!}, 0];
    financeChart.data.labels = ['0', ...{!! json_encode($labels) !!}, ' X'];
    financeChart.update();
</script>
@endsection
