@extends('layouts.admin')

@section('title', 'Detail Laporan #' . $complaint->tracking_code)
@section('page_title', 'Pemeriksaan & Tindak Lanjut Pengaduan')

@section('content')
<div style="margin-bottom: 18px;">
    <a href="{{ route('admin.complaints.index') }}" class="gov-btn gov-btn-outline" style="padding: 6px 14px; font-size: 13px;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pengaduan
    </a>
</div>

<div class="complaint-detail-grid">
    <!-- KOLOM KIRI: DATA LAPORAN & KRONOLOGI -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Card Detail Laporan -->
        <div class="admin-card">
            <div class="admin-card-header">
                <div>
                    <span class="ticket-code-pill">{{ $complaint->tracking_code }}</span>
                    <span class="status-pill {{ $complaint->status }}" style="margin-left: 8px;">
                        {{ $complaint->status_label }}
                    </span>
                </div>
                <div style="font-size: 12px; color: #64748b;">
                    <i class="bi bi-clock"></i> Masuk: {{ $complaint->created_at->format('d M Y, H:i') }} WIB
                </div>
            </div>

            <div style="padding: 24px 28px;">
                <span class="category-tag"><i class="bi bi-tag-fill"></i> {{ $complaint->category->name ?? 'Umum' }}</span>
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 10px 0 6px;">
                    {{ $complaint->subject }}
                </h2>
                @if($complaint->location)
                    <div style="font-size: 13px; color: #b45309; margin-bottom: 16px;">
                        <i class="bi bi-geo-alt-fill"></i> Lokasi Kejadian: {{ $complaint->location }}
                    </div>
                @endif

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; font-size: 14px; line-height: 1.7; color: #334155; margin-bottom: 20px;">
                    {!! nl2br(e($complaint->description)) !!}
                </div>

                <!-- Lampiran Bukti -->
                @if($complaint->attachments->count() > 0)
                    <div style="border-top: 1px solid #f1f5f9; padding-top: 16px;">
                        <h4 style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 10px;">
                            <i class="bi bi-paperclip"></i> Bukti Lampiran dari Pelapor ({{ $complaint->attachments->count() }})
                        </h4>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            @foreach($complaint->attachments as $att)
                                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="att-card-mini">
                                    <i class="bi bi-file-earmark-arrow-down-fill" style="font-size: 20px; color: #0b213f;"></i>
                                    <div>
                                        <div style="font-weight: 600; font-size: 12.5px; color: #0f172a;">{{ Str::limit($att->file_name, 22) }}</div>
                                        <div style="font-size: 11px; color: #64748b;">{{ strtoupper($att->file_type) }} &bull; {{ round($att->file_size / 1024, 1) }} KB</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card Riwayat Catatan & Tanggapan -->
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="bi bi-clock-history"></i>
                    <span>Log Riwayat Tindak Lanjut & Respon ({{ $complaint->responses->count() }})</span>
                </div>
            </div>

            <div style="padding: 20px 24px;">
                @if($complaint->responses->count() > 0)
                    <div class="audit-timeline">
                        @foreach($complaint->responses as $resp)
                            <div class="audit-item">
                                <div class="audit-avatar-wrap">
                                    <img src="{{ $resp->user->avatar_url ?? 'https://ui-avatars.com/api/?name=Petugas' }}" alt="Avatar" class="audit-avatar" referrerpolicy="no-referrer">
                                </div>
                                <div class="audit-content-box">
                                    <div class="audit-meta-header">
                                        <strong>{{ $resp->user->name ?? 'Petugas' }}</strong>
                                        <span class="audit-time">{{ $resp->created_at->format('d M Y, H:i') }} WIB</span>
                                    </div>
                                    @if($resp->status_change)
                                        <div style="margin: 4px 0 6px;">
                                            <span style="font-size: 11px; font-weight: 700; color: #b45309; background: #fef3c7; padding: 2px 8px; border-radius: 4px;">
                                                <i class="bi bi-arrow-repeat"></i> Status diubah ke: {{ $resp->status_change_label }}
                                            </span>
                                            @if($resp->is_public)
                                                <span style="font-size: 11px; color: #166534; background: #dcfce7; padding: 2px 7px; border-radius: 4px; margin-left: 4px;">
                                                    <i class="bi bi-eye"></i> Publik (Dilihat Pelapor)
                                                </span>
                                            @else
                                                <span style="font-size: 11px; color: #64748b; background: #f1f5f9; padding: 2px 7px; border-radius: 4px; margin-left: 4px;">
                                                    <i class="bi bi-eye-slash"></i> Catatan Internal
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                    <div style="font-size: 13.5px; color: #334155; line-height: 1.6;">
                                        {!! nl2br(e($resp->message)) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="padding: 20px; text-align: center; color: #64748b; font-size: 13px;">
                        Belum ada respon atau perubahan status yang dicatat.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: FORM UPDATE STATUS & DATA PELAPOR -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Card Update Status & Respon -->
        <div class="admin-card">
            <div class="admin-card-header" style="background: #08172c; color: #ffffff;">
                <div class="admin-card-title" style="color: #ffffff;">
                    <i class="bi bi-pencil-square" style="color: #fbbf24;"></i>
                    <span>Tindak Lanjut & Update Status</span>
                </div>
            </div>

            <form action="{{ route('admin.complaints.update-status', $complaint) }}" method="POST" style="padding: 22px;">
                @csrf
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Perbarui Status Pengaduan <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="status" id="statusSelect" required onchange="handleStatusChange(this.value)" class="gov-input">
                        <option value="pending" {{ $complaint->status == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi (Pending)</option>
                        <option value="in_progress" {{ $complaint->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses (In Progress)</option>
                        <option value="resolved" {{ $complaint->status == 'resolved' ? 'selected' : '' }}>Selesai Ditangani (Resolved)</option>
                        <option value="rejected" {{ $complaint->status == 'rejected' ? 'selected' : '' }}>Tolak Laporan (Rejected)</option>
                    </select>
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Tingkat Prioritas
                    </label>
                    <select name="priority" class="gov-input">
                        <option value="low" {{ $complaint->priority == 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ $complaint->priority == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ $complaint->priority == 'high' ? 'selected' : '' }}>Tinggi (Mendesak)</option>
                    </select>
                </div>

                <!-- Input Khusus Jika Ditolak -->
                <div id="rejectionReasonBox" style="display: {{ $complaint->status == 'rejected' ? 'block' : 'none' }}; margin-bottom: 14px;">
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #991b1b; margin-bottom: 6px;">
                        Alasan Penolakan Laporan <span style="color:#ef4444;">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="3" placeholder="Sebutkan alasan penolakan secara jelas kepada pelapor..." class="gov-input" style="border-color:#fca5a5;">{{ old('rejection_reason', $complaint->rejection_reason) }}</textarea>
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Tanggapan / Catatan Petugas <span style="color:#ef4444;">*</span> <small style="font-weight:400;color:#64748b;">(Min. 10 karakter)</small>
                    </label>
                    <textarea name="message" rows="4" required placeholder="Tuliskan tindakan perbaikan atau jawaban resmi untuk pelapor..." class="gov-input">{{ old('message') }}</textarea>
                    @error('message')
                        <span style="font-size: 12px; color: #dc2626;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="is_public" value="1" checked style="accent-color: #0b213f;">
                        <span>Tampilkan tanggapan ini ke pelapor di halaman lacak tiket</span>
                    </label>
                </div>

                <button type="submit" class="gov-btn gov-btn-primary" style="width: 100%; padding: 11px;">
                    <i class="bi bi-check-circle-fill"></i> Simpan Status & Kirim Respon
                </button>
            </form>
        </div>

        <!-- Card Data Kontak Pelapor -->
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Informasi Pelapor</span>
                </div>
                @if($complaint->is_anonymous)
                    <span style="font-size: 11px; font-weight: 700; color: #b45309; background: #fef3c7; padding: 2px 8px; border-radius: 999px;">
                        Permintaan Anonim
                    </span>
                @endif
            </div>

            <div style="padding: 18px 22px; font-size: 13.5px; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                    <span style="color: #64748b;">Nama Asli</span>
                    <strong>{{ $complaint->reporter_name }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                    <span style="color: #64748b;">Nomor Telepon</span>
                    @if($complaint->reporter_phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $complaint->reporter_phone) }}" target="_blank" style="color: #15803d; font-weight: 700; text-decoration: none;">
                            <i class="bi bi-whatsapp"></i> {{ $complaint->reporter_phone }}
                        </a>
                    @else
                        <span>-</span>
                    @endif
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                    <span style="color: #64748b;">Email</span>
                    <span>{{ $complaint->reporter_email ?: '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Akun Terdaftar</span>
                    <span>{{ $complaint->user->email ?? 'Google / Tamu' }}</span>
                </div>
            </div>
        </div>

        <!-- Tombol Hapus (Hanya Admin) -->
        @if(Auth::user()->isAdmin())
            <div style="text-align: right;">
                <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini beserta semua file lampirannya? Tindakan ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #dc2626; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="bi bi-trash"></i> Hapus Laporan Pengaduan
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<style>
.complaint-detail-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 24px;
}

.att-card-mini {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.15s ease;
}

.att-card-mini:hover {
    border-color: #0b213f;
    background: #eff6ff;
}

/* Audit Timeline */
.audit-timeline {
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: relative;
}

.audit-item {
    display: flex;
    gap: 12px;
}

.audit-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    object-fit: cover;
}

.audit-content-box {
    flex: 1;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
}

.audit-meta-header {
    display: flex;
    justify-content: space-between;
    font-size: 12.5px;
    margin-bottom: 2px;
}

.audit-time {
    font-size: 11px;
    color: #94a3b8;
}

@media (max-width: 980px) {
    .complaint-detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function handleStatusChange(status) {
    const box = document.getElementById('rejectionReasonBox');
    if (box) {
        box.style.display = (status === 'rejected') ? 'block' : 'none';
    }
}
</script>
@endsection
