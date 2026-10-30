<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Pengaduan DISPUSIP Bangkalan (Dinas Perpustakaan dan Kearsipan). Sampaikan aspirasi, saran, dan pengaduan layanan secara mudah dan transparan.">
    <title>@yield('title', 'Beranda') — Pengaduan DISPUSIP Bangkalan</title>

    <!-- Google Fonts: Barlow Condensed (Display), Bebas Neue, Inter (Body), JetBrains Mono (Meta) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,800;0,900;1,700&family=Bebas+Neue&family=Inter:wght@400;450;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Brutalist Editorial Design System -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    
    @stack('styles')
</head>
<body>

<!-- TOP GOVERNMENT EMBLEM BAR -->
<div class="gov-topbar">
    <div class="gov-topbar-inner">
        <div class="gov-badge-text">
            <span class="dot"></span>
            <span>PEMERINTAH KABUPATEN BANGKALAN &bull; PORTAL PENGADUAN DISPUSIP</span>
        </div>
        <div class="gov-links">
            <a href="https://bangkalankab.go.id" target="_blank" rel="noopener"><i class="bi bi-globe2"></i> bangkalankab.go.id</a>
            <span>&bull;</span>
            <a href="tel:0313095000"><i class="bi bi-telephone-fill"></i> (031) 3095000</a>
        </div>
    </div>
</div>

<!-- FLOATING PILL NAVBAR -->
<div class="nav-outer-wrapper">
    <nav class="main-navbar">
        <!-- Logo & Identitas -->
        <a href="{{ route('home') }}" class="brand-wrapper">
            <div class="brand-emblem">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <div class="brand-titles">
                <span class="brand-title-main">PENGADUAN DISPUSIP</span>
                <span class="brand-title-sub">Perpustakaan & Kearsipan Bangkalan</span>
            </div>
        </a>

        <!-- Tombol Menu Mobile -->
        <button class="mobile-nav-btn" onclick="toggleMobileNav()" aria-label="Buka Menu">
            <i class="bi bi-list"></i>
        </button>

        <!-- Navigasi -->
        <ul class="nav-menu" id="navMenu">
            <li>
                <a href="{{ route('home') }}" class="nav-link-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    Beranda
                </a>
            </li>
            <li>
                <a href="{{ route('tracking.index') }}" class="nav-link-item {{ request()->routeIs('tracking.*') ? 'active' : '' }}">
                    Lacak Tiket
                </a>
            </li>

            @guest
                <li>
                    <a href="{{ route('login') }}" class="nav-link-item {{ request()->routeIs('login') ? 'active' : '' }}">
                        Masuk
                    </a>
                </li>
                <li>
                    <a href="{{ route('register') }}" class="nav-btn-lapor">
                        <span>Buat Pengaduan</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('complaint.create') }}" class="nav-btn-lapor {{ request()->routeIs('complaint.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tulis Aduan</span>
                    </a>
                </li>

                @if(Auth::user()->isPetugas())
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="nav-link-item" style="color: var(--color-carbon-black); background: var(--color-mint-chip); font-weight: 600;">
                            <i class="bi bi-speedometer2"></i> Panel Petugas
                        </a>
                    </li>
                @endif

                <!-- User Dropdown -->
                <li class="nav-user-dropdown">
                    <a href="#" class="user-toggle">
                        <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar-tiny" referrerpolicy="no-referrer">
                        <span>{{ Str::limit(Auth::user()->name, 16) }}</span>
                        <i class="bi bi-chevron-down" style="font-size: 11px;"></i>
                    </a>
                    <div class="user-dropdown-menu">
                        <a href="{{ route('tracking.index') }}">
                            <i class="bi bi-ticket-perforated"></i> Riwayat Laporan
                        </a>
                        @if(Auth::user()->isPetugas())
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard Admin
                            </a>
                        @endif
                        <div class="menu-divider"></div>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('global-logout-form').submit();" style="color: #b91c1c;">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </a>
                        <form id="global-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </nav>
</div>

<!-- ALERT NOTIFIKASI GLOBAL -->
@if(session('success') || session('error') || session('warning') || session('info'))
    <div class="site-alerts">
        @if(session('success'))
            <div class="gov-alert gov-alert-success">
                <i class="bi bi-check-circle-fill" style="font-size: 18px;"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="gov-alert gov-alert-danger">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 18px;"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if(session('warning'))
            <div class="gov-alert gov-alert-warning">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 18px;"></i>
                <div>{{ session('warning') }}</div>
            </div>
        @endif
        @if(session('info'))
            <div class="gov-alert gov-alert-info">
                <i class="bi bi-info-circle-fill" style="font-size: 18px;"></i>
                <div>{{ session('info') }}</div>
            </div>
        @endif
    </div>
@endif

<!-- MAIN CONTENT -->
<main class="page-wrapper">
    @yield('content')
</main>

<!-- FOOTER TOP-ARC BRUTALIST -->
<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-col">
            <h4>Dinas Perpustakaan & Kearsipan Bangkalan</h4>
            <p>
                Layanan aspirasi dan pengaduan publik resmi untuk memastikan kualitas sarana literasi, kearsipan, dan fasilitas perpustakaan Kabupaten Bangkalan tetap prima, akuntabel, dan transparan.
            </p>
            <p style="font-family: var(--font-mono); font-size: 12px; color: var(--color-smoke); text-transform: uppercase;">
                <i class="bi bi-geo-alt-fill"></i> Jl. Soekarno-Hatta No. 35, Mlajah, Bangkalan, Jawa Timur 69116
            </p>
        </div>
        <div class="footer-col">
            <h4>Jam Operasional</h4>
            <p><strong>Senin — Kamis:</strong> 08.00 - 15.30 WIB</p>
            <p><strong>Jumat:</strong> 08.00 - 14.30 WIB</p>
            <p style="font-size: 13px; color: var(--color-smoke);">Pengaduan digital diterima 24 jam setiap hari.</p>
        </div>
        <div class="footer-col">
            <h4>Akses Cepat</h4>
            <ul class="footer-links">
                <li><a href="{{ route('home') }}">Beranda Publik</a></li>
                <li><a href="{{ route('tracking.index') }}">Lacak Status Tiket</a></li>
                <li><a href="{{ route('complaint.create') }}">Buat Laporan Baru</a></li>
                <li><a href="{{ route('login') }}">Masuk Akun</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; {{ date('Y') }} DISPUSIP BANGKALAN &bull; SISTEM PENGADUAN LAYANAN PUBLIK &bull; ALL RIGHTS RESERVED
    </div>
</footer>

<script>
function toggleMobileNav() {
    const nav = document.getElementById('navMenu');
    nav.classList.toggle('active');
}
</script>

@stack('scripts')
</body>
</html>
