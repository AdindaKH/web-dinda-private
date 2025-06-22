<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    {{-- css tersimpan pada folder public --}}
    <link rel="stylesheet" href="{{ asset('css/pimpinan.css') }}">

    {{-- jQuery CDN --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 

    <!-- Tambahkan di dalam tag <head> untuk CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
</head>

<body>
    <div class="sidebar">
        <img src="https://img.icons8.com/ios-filled/50/user.png" alt="user icon">
        {{-- jumlah (rp) pendapatan (barang keluar) dan pengeluaran (barang masuk dan gaji) --}}
        {{-- serta berisikan tabel dengan grafiks garis yang mena garis merah berupa pengeluaran dan garis biru pendapatan --}}
        <a href="/pimpinan/dashboard" class="sidebar-link">Dashboard</a>
        {{-- tabel laporan barang --}}
        <a href="/pimpinan/laporan-barang" class="sidebar-link">Laporan Barang</a> 
        {{-- tabel laporan pekerja --}}
        <a href="/pimpinan/laporan-karyawan" class="sidebar-link">Laporan Pekerja</a>
        {{-- tabel laporan supplier --}}
        <a href="/pimpinan/laporan-supplier" class="sidebar-link">Laporan Mitra Bisnis</a>
        {{-- tabel laporan transaksi --}}
        <a href="/pimpinan/laporan-transaksi" class="sidebar-link">Laporan Keuangan</a>
        
        {{-- <a href="/logout" class="sidebar-link-out">Logout</a> --}}
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        
        <a href="#" class="sidebar-link-out"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           Logout
        </a>
        
    </div>
    
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Tambahkan di bagian bawah sebelum penutup tag </body> untuk JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Moment.js -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- Daterangepicker JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // SweetAlert sukses
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        // SweetAlert error validasi
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                html: `{!! implode('<br>', $errors->all()) !!}`,
            });
        @endif
    </script>
</body>
</html>
