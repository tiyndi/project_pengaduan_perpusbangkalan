@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 16px;">
    <div>
        <p style="margin: 0; font-size: 13.5px; color: #64748b;">
            Kelola seluruh akun dalam sistem: Administrator, Petugas Perpustakaan, dan Akun Masyarakat / Pelapor.
        </p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="gov-btn gov-btn-primary" style="padding: 10px 18px; font-size: 13.5px;">
        <i class="bi bi-person-plus-fill"></i>
        <span>Tambah Pengguna Baru</span>
    </a>
</div>

<!-- FILTER TABS & SEARCH ROW -->
<div class="admin-card" style="margin-bottom: 20px; padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <!-- Filter Tabs Role -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('admin.users.index') }}" 
               class="gov-filter-pill {{ !request('role') ? 'active' : '' }}">
                Semua <span class="pill-count">{{ $counts['all'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'petugas']) }}" 
               class="gov-filter-pill {{ request('role') === 'petugas' ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Petugas <span class="pill-count">{{ $counts['petugas'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" 
               class="gov-filter-pill {{ request('role') === 'admin' ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> Admin <span class="pill-count">{{ $counts['admin'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'user']) }}" 
               class="gov-filter-pill {{ request('role') === 'user' ? 'active' : '' }}">
                <i class="bi bi-people"></i> Masyarakat <span class="pill-count">{{ $counts['masyarakat'] ?? 0 }}</span>
            </a>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 8px; margin: 0; min-width: 260px;">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <div style="position: relative; flex: 1;">
                <i class="bi bi-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama atau email..." 
                       class="gov-input" style="padding-left: 34px; padding-top: 8px; padding-bottom: 8px; font-size: 13px;">
            </div>
            @if(request('search'))
                <a href="{{ route('admin.users.index', request('role') ? ['role' => request('role')] : []) }}" class="gov-btn gov-btn-outline" style="padding: 8px 12px;" title="Reset Pencarian">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">
            <i class="bi bi-people-fill"></i>
            <span>Daftar Pengguna</span>
        </div>
        <span style="font-size: 12.5px; color: #64748b; font-weight: 600;">
            Menampilkan {{ $users->count() }} dari {{ $users->total() }} akun
        </span>
    </div>

    @if($users->count() > 0)
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Email & Identitas</th>
                        <th>Peran (Role)</th>
                        <th style="text-align: center;">Aktivitas</th>
                        <th>Status Akun</th>
                        <th>Tanggal Terdaftar</th>
                        <th style="text-align: center; width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($u->avatar_url)
                                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" style="width: 38px; height: 38px; border-radius: 10px; object-fit: cover; border: 1px solid #e2e8f0;" referrerpolicy="no-referrer">
                                    @else
                                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #0e243f; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <strong style="color: #0f172a; font-size: 14px;">{{ $u->name }}</strong>
                                            @if($u->id === Auth::id())
                                                <span style="font-size: 10px; background: #eff6ff; color: #1d4ed8; padding: 2px 6px; border-radius: 6px; font-weight: 700; border: 1px solid #bfdbfe;">
                                                    Akun Anda
                                                </span>
                                            @endif
                                        </div>
                                        @if($u->google_id)
                                            <div style="font-size: 11px; color: #15803d; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                                <i class="bi bi-google" style="font-size: 10px;"></i> Terhubung Google
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 13px; color: #334155; font-weight: 500;">{{ $u->email }}</span>
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge-role-admin">
                                        <i class="bi bi-shield-lock-fill"></i> Administrator
                                    </span>
                                @elseif($u->role === 'petugas')
                                    <span class="badge-role-petugas">
                                        <i class="bi bi-person-badge-fill"></i> Petugas
                                    </span>
                                @else
                                    <span class="badge-role-masyarakat">
                                        <i class="bi bi-people-fill"></i> Masyarakat
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($u->role === 'user')
                                    <span style="font-weight: 700; font-size: 13px; color: #0e243f;">{{ $u->complaints_count }}</span>
                                    <span style="font-size: 11px; color: #64748b; display: block;">aduan dibuat</span>
                                @else
                                    <span style="font-weight: 700; font-size: 13px; color: #0e243f;">{{ $u->responses_count }}</span>
                                    <span style="font-size: 11px; color: #64748b; display: block;">respon diberikan</span>
                                @endif
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span style="font-size: 11px; font-weight: 700; color: #166534; background: #dcfce7; padding: 3px 9px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="bi bi-check-circle-fill"></i> Aktif
                                    </span>
                                @else
                                    <span style="font-size: 11px; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 3px 9px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="bi bi-pause-circle-fill"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: #64748b;">
                                    {{ $u->created_at->format('d M Y') }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn-action-icon" title="Edit Akun Pengguna">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.toggle-active', $u->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn-action-icon" title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}" style="color: {{ $u->is_active ? '#d97706' : '#166534' }};">
                                                <i class="bi {{ $u->is_active ? 'bi-pause-circle-fill' : 'bi-play-circle-fill' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen akun {{ addslashes($u->name) }}? Data riwayat laporan tetap tersimpan.');" 
                                              style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon" style="color: #dc2626;" title="Hapus Permanen">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $users->links() }}
            </div>
        @endif
    @else
        <div style="padding: 48px; text-align: center; color: #64748b;">
            <i class="bi bi-person-x" style="font-size: 40px; color: #94a3b8; display: block; margin-bottom: 8px;"></i>
            Tidak ditemukan akun pengguna yang cocok dengan kriteria filter.
        </div>
    @endif
</div>
@endsection
