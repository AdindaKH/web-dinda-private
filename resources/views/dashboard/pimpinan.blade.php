@extends('layouts.app_pimpinan')

@section('title', 'Dashboard Pimpinan')

@section('content')
<div class="container mt-4">
    <div class="title-box">
        <h3 class="fw-bold m-0 py-3">Dashboard</h3>
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
                                value="{{ request('start_date', $startDate) }}"
                                class="filter-info border px-2 py-1 rounded text-sm">
                            <input style="width: fit-content" type="date" name="end_date" id="end_date" 
                                value="{{ request('end_date', $endDate) }}"
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
    let kloterChart;
    const ctxKloter = document.getElementById('kloterChart').getContext('2d');
    kloterChart = new Chart(ctxKloter, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_reverse($labelsKloter)) !!},
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
                fetchChartData();
            }
        });
    });

    document.getElementById('kloterFilter').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const start = selected.getAttribute('data-start');
        const end = selected.getAttribute('data-end');
        if (start && end) {
            startInput.value = start;
            endInput.value = end;
        }
        fetchChartData();
    });

    // AJAX Chart Update
    const updateCharts = (data) => {
        // Update Chart Tanggal
        financeChart.data.labels = data.labels;
        financeChart.data.datasets[0].data = data.pendapatanBulanan;
        financeChart.data.datasets[1].data = data.pengeluaranBulanan;
        financeChart.update();

        // Update DOM Keuangan
        document.querySelector('.info-box .text-success').textContent = 
            'Rp ' + data.keuangan.pendapatan.toLocaleString('id-ID');
        document.querySelector('.info-box .text-danger').textContent = 
            'Rp ' + data.keuangan.pengeluaran.toLocaleString('id-ID');

        // Update Chart Kloter
        if (kloterChart) {
            kloterChart.destroy();
        }

        const kloterId = document.getElementById('kloterFilter').value;
        const chartType = kloterId ? 'bar' : 'line';

        // Buat ulang chart kloter dengan tipe sesuai filter
        const ctxKloter = document.getElementById('kloterChart').getContext('2d');
        kloterChart = new Chart(ctxKloter, {
            type: chartType,
            data: {
                labels: data.labelsKloter.reverse(),
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: data.pendapatanKloter.reverse(),
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                        fill: chartType === 'bar',
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'blue',
                        borderWidth: 3,
                        showLine: chartType === 'line',
                    },
                    {
                        label: 'Pengeluaran',
                        data: data.pengeluaranKloter.reverse(),
                        borderColor: 'red',
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        fill: chartType === 'bar',
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'red',
                        borderWidth: 4,
                        showLine: chartType === 'line',
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
    };

    const fetchChartData = () => {
        const startDate = startInput.value;
        const endDate = endInput.value;
        const kloterId = document.getElementById('kloterFilter').value;

        fetch("{{ route('dashboard.pimpinan.data') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                start_date: startDate,
                end_date: endDate,
                kloter_id: kloterId
            })
        })
        .then(res => res.json())
        .then(updateCharts)
        .catch(err => console.error('Gagal fetch data:', err));
    };

    // Event listener input
    startInput.addEventListener('change', fetchChartData);
    endInput.addEventListener('change', fetchChartData);
    document.getElementById('kloterFilter').addEventListener('change', fetchChartData);
</script>
@endsection
