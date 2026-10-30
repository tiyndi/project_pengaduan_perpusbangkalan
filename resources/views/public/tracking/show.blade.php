@extends('layouts.app')

@section('title', 'Detail Tiket #' . $complaint->tracking_code)

@section('content')
<div style="max-width: 860px; margin: 0 auto 60px;">
    <!-- Tombol Kembali -->
    <div style="margin-bottom: 24px;">
        <a href="{{ route('tracking.index') }}" class="btn-ghost" style="padding: 8px 16px; font-size: 13px;">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Daftar Laporan</span>
        </a>
    </div>

    <!-- KARTU UTAMA DETAIL -->
    <div class="card-flat" style="margin-bottom: 32px; padding: 0; overflow: hidden;">
        <!-- Header Kartu -->
        <div style="background-color: var(--color-mist-gray); border-bottom: 1px solid var(--color-ash); padding: 18px 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="ticket-code-pill" style="font-size: 14px; background: var(--color-paper-white); border: 1px solid var(--color-ash);">
                    {{ $complaint->tracking_code }}
                </span>
                @if($complaint->status == 'resolved')
                    <span class="badge-status badge-status-resolved"><i class="bi bi-check2"></i> Selesai</span>
                @elseif($complaint->status == 'in_progress')
                    <span class="badge-status badge-status-in_progress">Diproses</span>
                @elseif($complaint->status == 'rejected')
                    <span class="badge-status badge-status-rejected">Ditolak</span>
                @else
                    <span class="badge-status badge-status-pending">Menunggu</span>
                @endif
            </div>
            <div class="font-mono" style="font-size: 12px; color: var(--color-smoke); text-transform: uppercase;">
                Diajukan pada {{ $complaint->created_at->format('d/m/Y, H:i') }} WIB
            </div>
        </div>

        <div style="padding: 32px 30px; display: flex; flex-direction: column; gap: 28px;">
            <!-- JUDUL & KATEGORI -->
            <div>
                <span class="section-tag" style="margin-bottom: 10px;">{{ $complaint->category->name ?? 'Umum' }}</span>
                <h1 style="font-size: clamp(24px, 3.5vw, 36px); margin-bottom: 8px;">{{ $complaint->subject }}</h1>
                @if($complaint->location)
                    <div style="font-family: var(--font-mono); font-size: 12.5px; color: var(--color-slate); text-transform: uppercase;">
                        <i class="bi bi-geo-alt-fill"></i> Lokasi: {{ $complaint->location }}
                    </div>
                @endif
            </div>

            <!-- STEP PROGRESS TIMELINE — BRUTALIST -->
            <div style="background-color: var(--color-mist-gray); border: 1px solid var(--color-ash); border-radius: var(--radius-card); padding: 24px;">
                <h4 style="font-size: 16px; text-transform: uppercase; margin-bottom: 20px;">
                    PROGRES PENANGANAN
                </h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; text-align: center;">
                    <!-- Step 1 -->
                    <div style="background: var(--color-paper-white); border: 1.5px solid var(--color-carbon-black); border-radius: 12px; padding: 14px 10px;">
                        <span class="font-mono" style="font-size: 11px; color: var(--color-smoke); display: block; margin-bottom: 4px;">TAHAP 01</span>
                        <strong style="display: block; font-size: 13px; text-transform: uppercase;">Laporan Diterima</strong>
                        <span class="font-mono" style="font-size: 11px; color: var(--color-smoke);">{{ $complaint->created_at->format('d/m, H:i') }}</span>
                    </div>

                    <!-- Step 2 -->
                    <div style="background: {{ in_array($complaint->status, ['in_progress', 'resolved', 'rejected']) ? 'var(--color-paper-white)' : 'var(--color-mist-gray)' }}; border: 1.5px solid {{ in_array($complaint->status, ['in_progress', 'resolved', 'rejected']) ? 'var(--color-carbon-black)' : 'var(--color-ash)' }}; border-radius: 12px; padding: 14px 10px;">
                        <span class="font-mono" style="font-size: 11px; color: var(--color-smoke); display: block; margin-bottom: 4px;">TAHAP 02</span>
                        <strong style="display: block; font-size: 13px; text-transform: uppercase;">Tindak Lanjut</strong>
                        <span class="font-mono" style="font-size: 11px; color: var(--color-smoke);">
                            {{ $complaint->status == 'pending' ? 'Dalam Antrean' : 'Sedang Diperiksa' }}
                        </span>
                    </div>

                    <!-- Step 3 -->
                    <div style="background: {{ in_array($complaint->status, ['resolved', 'rejected']) ? ( $complaint->status == 'resolved' ? 'var(--color-mint-chip)' : 'var(--color-carbon-black)' ) : 'var(--color-mist-gray)' }}; color: {{ $complaint->status == 'rejected' ? 'var(--color-paper-white)' : 'var(--color-carbon-black)' }}; border: 1.5px solid {{ in_array($complaint->status, ['resolved', 'rejected']) ? 'transparent' : 'var(--color-ash)' }}; border-radius: 12px; padding: 14px 10px;">
                        <span class="font-mono" style="font-size: 11px; display: block; margin-bottom: 4px; opacity: 0.8;">TAHAP 03</span>
                        <strong style="display: block; font-size: 13px; text-transform: uppercase;">
                            {{ $complaint->status == 'rejected' ? 'Laporan Ditolak' : 'Tuntas Selesai' }}
                        </strong>
                        <span class="font-mono" style="font-size: 11px; opacity: 0.8;">
                            {{ $complaint->resolved_at ? $complaint->resolved_at->format('d/m, H:i') : 'Menunggu Hasil' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- DESKRIPSI LENGKAP -->
            <div>
                <h4 style="font-size: 18px; text-transform: uppercase; margin-bottom: 12px; border-bottom: 1px solid var(--color-mist-gray); padding-bottom: 8px;">
                    URAIAN PENGADUAN
                </h4>
                <div style="font-size: 15px; color: var(--color-carbon-black); line-height: 1.6;">
                    {!! nl2br(e($complaint->description)) !!}
                </div>
            </div>

            <!-- LAMPIRAN BUKTI (JIKA ADA) -->
            @if($complaint->attachments->count() > 0)
                <div>
                    <h4 style="font-size: 18px; text-transform: uppercase; margin-bottom: 12px; border-bottom: 1px solid var(--color-mist-gray); padding-bottom: 8px;">
                        LAMPIRAN BUKTI ({{ $complaint->attachments->count() }})
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px;">
                        @foreach($complaint->attachments as $att)
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="card-flat" style="padding: 14px; display: flex; align-items: center; gap: 12px; transition: background 0.15s ease;">
                                <i class="bi bi-file-earmark-arrow-down" style="font-size: 24px; color: var(--color-carbon-black);"></i>
                                <div style="min-width: 0;">
                                    <strong style="display: block; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $att->file_name }}</strong>
                                    <span class="font-mono" style="font-size: 11px; color: var(--color-smoke); text-transform: uppercase;">{{ strtoupper($att->file_type) }} &bull; {{ round($att->file_size / 1024, 1) }} KB</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- ALASAN PENOLAKAN JIKA DITOLAK -->
            @if($complaint->status == 'rejected' && $complaint->rejection_reason)
                <div class="gov-alert gov-alert-danger">
                    <i class="bi bi-x-circle" style="font-size: 22px;"></i>
                    <div>
                        <strong>LAPORAN INI DITOLAK DENGAN ALASAN:</strong>
                        <p style="margin-top: 4px; font-size: 13.5px;">{{ $complaint->rejection_reason }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- TANGGAPAN RESMI PETUGAS -->
    <div class="card-flat" style="padding: 32px 30px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; border-bottom: 1px solid var(--color-mist-gray); padding-bottom: 14px;">
            <h3 style="font-size: 22px; text-transform: uppercase; margin: 0;">
                TANGGAPAN RESMI PETUGAS
            </h3>
            <span class="font-mono" style="font-size: 12px; color: var(--color-smoke); text-transform: uppercase;">
                {{ $complaint->publicResponses->count() }} TANGGAPAN
            </span>
        </div>

        @if($complaint->publicResponses->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($complaint->publicResponses as $response)
                    <div style="background-color: var(--color-mist-gray); border: 1px solid var(--color-ash); border-radius: var(--radius-card); padding: 22px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ $response->user->avatar_url ?? 'https://ui-avatars.com/api/?name=Petugas&background=000&color=fff' }}" alt="Petugas" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;" referrerpolicy="no-referrer">
                                <div>
                                    <strong style="font-size: 14px; text-transform: uppercase;">{{ $response->user->name ?? 'Petugas DISPUSIP' }}</strong>
                                    <div class="font-mono" style="font-size: 11px; color: var(--color-slate); text-transform: uppercase;">PETUGAS RESMI DISPUSIP</div>
                                </div>
                            </div>
                            <span class="font-mono" style="font-size: 11.5px; color: var(--color-smoke);">{{ $response->created_at->format('d/m/Y, H:i') }} WIB</span>
                        </div>
                        <div style="font-size: 14.5px; color: var(--color-carbon-black); line-height: 1.55;">
                            {!! nl2br(e($response->message)) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 32px 16px; color: var(--color-slate);">
                <i class="bi bi-hourglass-split" style="font-size: 28px; display: block; margin-bottom: 8px; color: var(--color-carbon-black);"></i>
                <p style="font-size: 14px; margin: 0;">Belum ada tanggapan resmi dari petugas. Laporan Anda sedang dalam tahap verifikasi tim kami.</p>
            </div>
        @endif
    </div>
</div>
@endsection
