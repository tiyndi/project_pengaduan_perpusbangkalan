@extends('layouts.app')

@section('title', 'Laporan Berhasil Dikirim')

@section('content')
<div style="max-width: 680px; margin: 20px auto 40px;">
    <div class="card-flat" style="padding: 48px 40px; text-align: center;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background-color: var(--color-mint-chip); color: var(--color-carbon-black); display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 20px;">
            <i class="bi bi-check-lg"></i>
        </div>

        <span class="section-tag">TERCATAT RESMI</span>
        <h1 style="font-size: clamp(32px, 4vw, 44px); margin: 12px 0 10px;">LAPORAN BERHASIL DIKIRIM</h1>
        <p style="font-size: 15px; color: var(--color-slate); max-width: 500px; margin: 0 auto 32px; line-height: 1.5;">
            Terima kasih atas kepedulian Anda. Petugas piket DISPUSIP Bangkalan akan segera memverifikasi dan memperbarui status penanganan tiket Anda.
        </p>

        <!-- KOTAK KODE TRACKING — MONOSPACE DISPLAY -->
        <div style="background-color: var(--color-mist-gray); border: 1.5px solid var(--color-ash); border-radius: var(--radius-card); padding: 28px 24px; margin-bottom: 32px;">
            <span class="font-mono" style="font-size: 11.5px; text-transform: uppercase; color: var(--color-smoke); letter-spacing: 0.05em; display: block; margin-bottom: 6px;">
                NOMOR TIKET PELACAKAN ANDA
            </span>
            <div id="trackingCodeText" class="font-mono" style="font-size: 36px; font-weight: 700; color: var(--color-carbon-black); letter-spacing: 0.08em; margin-bottom: 10px;">
                {{ $complaint->tracking_code }}
            </div>
            <p style="font-size: 13px; color: var(--color-slate); margin-bottom: 16px;">
                Gunakan nomor tiket ini untuk mengecek respon petugas dan perkembangan penanganan.
            </p>
            <button type="button" class="btn-ghost" onclick="copyTrackingCode()" id="btnCopyCode" style="font-size: 13px; padding: 8px 18px;">
                <i class="bi bi-clipboard"></i>
                <span id="copyBtnLabel">Salin Nomor Tiket</span>
            </button>
        </div>

        <!-- RINGKASAN DATA LAPORAN -->
        <div style="text-align: left; background-color: var(--color-paper-white); border: 1px solid var(--color-ash); border-radius: var(--radius-card); padding: 24px; margin-bottom: 32px;">
            <h3 style="font-size: 18px; text-transform: uppercase; margin-bottom: 16px; border-bottom: 1px solid var(--color-mist-gray); padding-bottom: 10px;">
                RINGKASAN PENGADUAN
            </h3>
            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-mist-gray); padding-bottom: 8px;">
                    <span style="color: var(--color-smoke); font-family: var(--font-mono); text-transform: uppercase; font-size: 12px;">KATEGORI</span>
                    <strong>{{ $complaint->category->name ?? 'Umum' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-mist-gray); padding-bottom: 8px;">
                    <span style="color: var(--color-smoke); font-family: var(--font-mono); text-transform: uppercase; font-size: 12px;">JUDUL ADUAN</span>
                    <span style="max-width: 320px; text-align: right;">{{ $complaint->subject }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-mist-gray); padding-bottom: 8px;">
                    <span style="color: var(--color-smoke); font-family: var(--font-mono); text-transform: uppercase; font-size: 12px;">STATUS</span>
                    <span class="badge-status badge-status-pending">Menunggu Verifikasi</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-smoke); font-family: var(--font-mono); text-transform: uppercase; font-size: 12px;">WAKTU KIRIM</span>
                    <span class="font-mono" style="font-size: 13px;">{{ $complaint->created_at->format('d/m/Y, H:i') }} WIB</span>
                </div>
            </div>
        </div>

        <!-- TOMBOL AKSI -->
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('tracking.show') }}?tracking_code={{ $complaint->tracking_code }}" class="btn-dark">
                <i class="bi bi-search"></i>
                <span>Lacak Status Tiket Ini</span>
            </a>
            <a href="{{ route('complaint.create') }}" class="btn-ghost">
                <i class="bi bi-plus-lg"></i>
                <span>Tulis Laporan Lain</span>
            </a>
            <a href="{{ route('home') }}" class="btn-ghost">
                <i class="bi bi-house"></i>
                <span>Ke Beranda</span>
            </a>
        </div>
    </div>
</div>

<script>
function copyTrackingCode() {
    const code = document.getElementById('trackingCodeText').innerText.trim();
    navigator.clipboard.writeText(code).then(() => {
        const label = document.getElementById('copyBtnLabel');
        label.innerText = 'Nomor Tiket Tersalin!';
        setTimeout(() => {
            label.innerText = 'Salin Nomor Tiket';
        }, 2500);
    });
}
</script>
@endsection
