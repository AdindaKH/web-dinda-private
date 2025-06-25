<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Tambahkan di dalam tag <head> untuk CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- css tersimpan pada folder public --}}
    <link rel="stylesheet" href="{{ asset('css/operator.css') }}">
    <link rel="stylesheet" href="{{ asset('css/extra.css') }}">

</head>

<body>
    <div class="sidebar">
        <img src="https://img.icons8.com/ios-filled/50/user.png" alt="user icon">

        <!-- Dashboard -->
        <a href="/operator/dashboard" class="sidebar-link">Dashboard</a>

        <!-- Kategori: Data -->
        <div class="sidebar-category">Data</div>
        <a href="/operator/barang" class="sidebar-link">Barang</a>
            <div class="sidebar-submenu">
                <a href="{{ route('barang.index', ['filter' => 'produk']) }}" class="sidebar-link">Produk</a>
                <a href="{{ route('barang.index', ['filter' => 'pendukung']) }}" class="sidebar-link">Pendukung</a>
            </div>
        <a href="/operator/suplier" class="sidebar-link">Mitra Bisnis</a>
        <a href="/operator/karyawan" class="sidebar-link">Karyawan</a>
        <a href="/operator/gaji" class="sidebar-link">Gaji</a>
        
        <!-- Kategori: Aktivitas -->
        <div class="sidebar-category">Aktivitas</div>
        
        @php
            $latestKloterId = \App\Models\Kloter::max('id');
        @endphp
        <a href="{{ route('presensi.index', ['kloter_id' => $latestKloterId]) }}" class="sidebar-link">Presensi</a>

        <a href="/operator/transaksi" class="sidebar-link">Transaksi</a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>        
        <a href="#" class="sidebar-link-out"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           Logout
        </a>

    </div>

    {{-- <div class="wrapper"> --}}
        {{-- NAVBAR --}}
        {{-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-4">
            <a class="navbar-brand text-white" href="#">App Name</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="#"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav> --}}

    <div class="main-content">
        {{-- Konten utama --}}
        @yield('content')
    </div>

    {{-- </div> --}}

    {{-- js untuk boostarp --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    {{-- <script>
        function updateMainContentPosition() {
            const mainContent = document.querySelector('.main-content');
            const sidebar = document.querySelector('.sidebar');

            if (!mainContent || !sidebar) return;

            if (window.innerWidth < 600) {
                const sidebarHeight = sidebar.offsetHeight;
                mainContent.style.marginTop = sidebarHeight + "px";
                mainContent.style.marginLeft = "0px";
            } else {
                mainContent.style.marginTop = "0px";
                mainContent.style.marginLeft = "220px";
            }
        }

        window.addEventListener('load', updateMainContentPosition);
        window.addEventListener('resize', updateMainContentPosition);
    </script> --}}



</body>

</html>
