@extends('layouts.app_pimpinan')

@section('title', 'Dashboard Pimpinan')

@section('content')
<div class="container mt-4">
    <div class="title-box">
        <h3 class="fw-bold m-0">Dashboard</h3>
    </div>

    <div class="dashboard-container">
        <div class="info-row">
            <div class="info-box">
                <div>Pendapatan</div>
                <div class="text-success mt-2">
                    Rp {{ number_format($keuangan['pendapatan'], 0, ',', '.') }}
                </div>
            </div>
            <div class="info-box">
                <div>Pengeluaran</div>
                <div class="text-danger mt-2">
                    Rp {{ number_format($keuangan['pengeluaran'], 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title mb-2">Grafik Pendapatan <span class="text-dark">vs</span> Pengeluaran</div>
            <canvas id="financeChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
                    tension: 0.4
                },
                {
                    label: 'Pengeluaran',
                    data: {!! json_encode($pengeluaranBulanan) !!},
                    borderColor: 'red',
                    backgroundColor: 'red',
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
</script>
@endsection
