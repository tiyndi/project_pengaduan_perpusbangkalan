<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Petugas') — Pengaduan DISPUSIP Bangkalan</title>

    <!-- Google Fonts: Barlow Condensed (Display), Inter (Body), JetBrains Mono (Meta) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,800;0,900;1,700&family=Bebas+Neue&family=Inter:wght@400;450;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Admin & Main Design System -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>
<body>

<div class="admin-layout">
    <!-- SIDEBAR -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Logo Instansi -->
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand-box">
            <div class="sidebar-emblem">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="sidebar-brand-title">DISPUSIP</div>
                <div class="sidebar-brand-sub">BANGKALAN &bull; ADMIN</div>
            </div>
        </a>

        <!-- Menu Navigasi -->
        <ul class="sidebar-menu">
            <li class="menu-category-title">Menu Utama</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.complaints.index') }}" class="sidebar-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
                    <i class="bi bi-inbox-fill"></i>
                    <span>Daftar Pengaduan</span>
                    @php
                        $pendingCount = \App\Models\Complaint::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="sidebar-counter">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>

            <li class="menu-category-title">Pengaturan Sistem</li>
            <li>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i>
                    <span>Kategori Aduan</span>
                </a>
            </li>

            @if(Auth::user()->isAdmin())
                <li>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Manajemen Pengguna</span>
                    </a>
                </li>
            @endif
        </ul>

        <!-- Profil Pengguna Sedang Login -->
        <div class="sidebar-user-card">
            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="sidebar-user-avatar" referrerpolicy="no-referrer">
            <div class="sidebar-user-info">
                <span class="sidebar-user-name">{{ Auth::user()->name }}</span>
                <span class="sidebar-user-role">
                    @if(Auth::user()->isAdmin())
                        <span style="color: var(--color-voltage-yellow);"><i class="bi bi-shield-lock-fill"></i> ADMIN</span>
                    @elseif(Auth::user()->isPetugas())
                        <span style="color: var(--color-mint-chip);"><i class="bi bi-person-badge-fill"></i> PETUGAS</span>
                    @else
                        <span>MASYARAKAT</span>
                    @endif
                </span>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="admin-main">
        <!-- TOPBAR -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-nav-btn" onclick="toggleAdminSidebar()" style="display:none;" id="mobileSidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-page-title">
                    @yield('page_title', 'Panel Petugas — Pengaduan DISPUSIP Bangkalan')
                </div>
            </div>

            <div class="topbar-right">
                <a href="{{ route('home') }}" class="btn-topbar-portal" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Portal Publik</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-topbar-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- ALERTS -->
        @if(session('success') || session('error') || session('warning') || session('info'))
            <div style="padding: 24px 36px 0;">
                @if(session('success'))
                    <div class="gov-alert gov-alert-success">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="gov-alert gov-alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        <!-- BODY CONTENT -->
        <main class="admin-body">
            @yield('content')
        </main>
    </div>
</div>

<script>
function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    sidebar.classList.toggle('open');
}
</script>

@stack('scripts')
</body>
</html>
