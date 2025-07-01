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

        <!-- Tab Filter -->
        <ul class="nav nav-tabs mt-4" id="chartTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tanggal-tab" data-bs-toggle="tab" data-bs-target="#tanggalChartTab" type="button" role="tab">Grafik Tanggal</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kloter-tab" data-bs-toggle="tab" data-bs-target="#kloterChartTab" type="button" role="tab">Grafik Kloter</button>
            </li>
        </ul>

        <div class="tab-content" id="chartTabContent">
            <!-- Grafik Tanggal -->
            <div class="tab-pane fade show active" id="tanggalChartTab" role="tabpanel">
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
                        </form>
                    </div>
                    <div class="chart-card">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Grafik Kloter -->
            <div class="tab-pane fade" id="kloterChartTab" role="tabpanel">
                <div class="chart-card bg-yellow-50 p-4 rounded shadow-md mt-4" style="height:auto;">
                    <div class="chart-c flex justify-between items-center mb-4">
                        <h2 class="font-bold text-lg">
                            Grafik Pendapatan <span class="text-dark">vs</span> Pengeluaran per Kloter
                        </h2>
                        <form id="kloter-filter-form" class="flex items-center gap-2" style="padding-top: 10px">
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
                        <canvas id="kloterChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Tanggal
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
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'blue',
                    borderWidth: 3,
                    showLine: true,
                },
                {
                    label: 'Pengeluaran',
                    data: {!! json_encode($pengeluaranBulanan) !!},
                    borderColor: 'red',
                    backgroundColor: 'red',
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'red',
                    borderWidth: 4,
                    showLine: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            elements: {
                line: {
                    cubicInterpolationMode: 'monotone',
                }
            },
            spanGaps: true,
            scales: {
                y: {
                    // alasan menggunakan logarithmic: untuk menghindari nilai yang terlalu besar atau kecil yang bisa membuat grafik sulit dibaca
                    // jika tidak ingin menggunakan logarithmic, bisa dihapus atau diganti dengan type: 'linear',
                    // jika ingin tetap menggunakan logarithmic, pastikan data tidak mengandung nilai nol atau negatif
                    // jika ada nilai nol atau negatif, bisa menggunakan filter untuk menghapusnya sebelum membuat grafik
                    type: 'logarithmic',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Grafik Kloter
    const ctxKloter = document.getElementById('kloterChart').getContext('2d');
    const kloterChart = new Chart(ctxKloter, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_reverse($labelsKloter)) !!}, // diurut terbalik
            datasets: [
                {
                    label: 'Pendapatan',
                    data: {!! json_encode(array_reverse($pendapatanKloter)) !!},
                    borderColor: 'blue',
                    backgroundColor: 'blue',
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'blue',
                    borderWidth: 3,
                    showLine: true,
                },
                {
                    label: 'Pengeluaran',
                    data: {!! json_encode(array_reverse($pengeluaranKloter)) !!},
                    borderColor: 'red',
                    backgroundColor: 'red',
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'red',
                    borderWidth: 4,
                    showLine: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            elements: {
                line: {
                    cubicInterpolationMode: 'monotone',
                }
            },
            spanGaps: true,
            scales: {
                y: {
                    // type: 'logarithmic',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Filter tanggal
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
        form.submit();
    });
</script>
@endsection
