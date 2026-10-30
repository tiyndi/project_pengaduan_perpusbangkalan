@extends('layouts.app')

@section('title', 'Tulis Pengaduan Baru')

@section('content')
<div class="form-container-wrap">
    <div class="gov-form-card">
        <!-- Header Form -->
        <div class="gov-form-header">
            <span class="section-tag" style="margin-bottom: 12px;">FORMULIR RESMI</span>
            <h1>SAMPAIKAN ASPIRASI & PENGADUAN ANDA</h1>
            <p>Lengkapi formulir di bawah ini dengan jelas dan valid untuk mempercepat tindak lanjut petugas DISPUSIP Bangkalan.</p>
        </div>

        <form action="{{ route('complaint.store') }}" method="POST" enctype="multipart/form-data" class="gov-form-body">
            @csrf

            <!-- Bagian 1: Kategori -->
            <div class="form-section">
                <label class="section-label">
                    <span>1. PILIH KATEGORI PENGADUAN <span class="req">*</span></span>
                    <small>Pilih bidang layanan yang sesuai dengan kendala Anda</small>
                </label>
                
                <div class="category-selection-grid">
                    @foreach($categories as $cat)
                        <label class="cat-pill-label">
                            <input type="radio" name="category_id" value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'checked' : '' }} required>
                            <span class="cat-pill-box">
                                <i class="bi {{ $cat->icon ?: 'bi-tag-fill' }}"></i>
                                <span class="cat-name">{{ $cat->name }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('category_id')
                    <span class="field-error-msg">{{ $message }}</span>
                @enderror
            </div>

            <!-- Bagian 2: Pokok Pengaduan -->
            <div class="form-section">
                <label class="section-label">
                    <span>2. POKOK PERMASALAHAN & KRONOLOGI <span class="req">*</span></span>
                    <small>Uraikan pokok keluhan dan rincian kejadian secara runtut</small>
                </label>

                <div class="form-group-field">
                    <label for="subject" class="sub-label">Judul Pengaduan <span class="req">*</span> <span class="font-mono" style="font-size: 11px; color: var(--color-smoke); text-transform: none;">(Min. 10 karakter)</span></label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                           placeholder="Contoh: Lampu Ruang Baca Lantai 2 Berkedip dan Mengganggu Pembaca"
                           class="gov-input @error('subject') is-invalid @enderror">
                    @error('subject')
                        <span class="field-error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group-field">
                    <label for="description" class="sub-label">Deskripsi Lengkap Aduan <span class="req">*</span> <span class="font-mono" style="font-size: 11px; color: var(--color-smoke); text-transform: none;">(Min. 20 karakter)</span></label>
                    <textarea id="description" name="description" rows="5" required
                              placeholder="Uraikan waktu kejadian, kronologi, dampak terhadap kenyamanan, serta usulan solusi perbaikan..."
                              class="gov-input gov-textarea @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="field-error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group-field">
                    <label for="location" class="sub-label">Lokasi / Ruangan Spesifik</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                           placeholder="Contoh: Lantai 2, Area Referensi Meja Pojok Barat"
                           class="gov-input @error('location') is-invalid @enderror">
                    @error('location')
                        <span class="field-error-msg">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Bagian 3: Identitas Pelapor -->
            <div class="form-section">
                <label class="section-label">
                    <span>3. DATA KONTAK PELAPOR <span class="req">*</span></span>
                    <small>Digunakan untuk klarifikasi dan pemberitahuan hasil respon petugas</small>
                </label>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group-field">
                        <label for="reporter_name" class="sub-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="reporter_name" name="reporter_name" value="{{ old('reporter_name', Auth::user()->name ?? '') }}" required
                               placeholder="Nama lengkap Anda"
                               class="gov-input @error('reporter_name') is-invalid @enderror">
                        @error('reporter_name')
                            <span class="field-error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group-field">
                        <label for="reporter_phone" class="sub-label">Nomor WhatsApp / HP Aktif <span class="req">*</span></label>
                        <input type="tel" id="reporter_phone" name="reporter_phone" value="{{ old('reporter_phone') }}" required
                               placeholder="081234567890"
                               class="gov-input @error('reporter_phone') is-invalid @enderror">
                        @error('reporter_phone')
                            <span class="field-error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-field">
                    <label for="reporter_email" class="sub-label">Alamat Email Pelapor</label>
                    <input type="email" id="reporter_email" name="reporter_email" value="{{ old('reporter_email', Auth::user()->email ?? '') }}"
                           placeholder="nama@email.com"
                           class="gov-input @error('reporter_email') is-invalid @enderror">
                    @error('reporter_email')
                        <span class="field-error-msg">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Bagian 4: Lampiran Bukti -->
            <div class="form-section">
                <label class="section-label">
                    <span>4. LAMPIRAN BUKTI PENDUKUNG</span>
                    <small>Unggah foto atau dokumen bukti kendala (Maks. 5 file, format JPG/PNG/PDF, maks 5MB/file)</small>
                </label>

                <div class="file-upload-dropzone" onclick="document.getElementById('attachments').click()">
                    <i class="bi bi-cloud-arrow-up" style="font-size: 32px; color: var(--color-carbon-black); display: block; margin-bottom: 8px;"></i>
                    <strong style="text-transform: uppercase; font-size: 14px;">Pilih File Bukti dari Perangkat Anda</strong>
                    <div class="font-mono" style="font-size: 11.5px; color: var(--color-smoke); margin-top: 4px; text-transform: uppercase;">
                        FORMAT: JPG, PNG, GIF, PDF (MAKS. 5MB)
                    </div>
                    <input type="file" id="attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf" style="display:none;" onchange="showFileNames(this)">
                </div>
                <div id="fileListContainer" style="margin-top: 12px; font-family: var(--font-mono); font-size: 12px; color: var(--color-slate);"></div>
                @error('attachments')
                    <span class="field-error-msg">{{ $message }}</span>
                @enderror
                @error('attachments.*')
                    <span class="field-error-msg">{{ $message }}</span>
                @enderror
            </div>

            <!-- Bagian 5: Privasi Anonim -->
            <div class="form-section">
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                    <input type="checkbox" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }} style="accent-color: var(--color-carbon-black); width: 18px; height: 18px; margin-top: 2px;">
                    <div>
                        <strong style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.02em;">KIRIM SEBAGAI LAPORAN ANONIM</strong>
                        <p style="font-size: 13px; color: var(--color-smoke); margin-top: 2px;">Nama Anda tidak akan ditampilkan di riwayat aduan terbuka demi perlindungan data pribadi.</p>
                    </div>
                </label>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-top: 10px;">
                <a href="{{ route('home') }}" class="btn-ghost">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <button type="submit" class="btn-dark" style="padding: 14px 28px; font-size: 15px;">
                    <i class="bi bi-send-fill"></i>
                    <span>Kirim Pengaduan Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showFileNames(input) {
    const container = document.getElementById('fileListContainer');
    if (!input.files || input.files.length === 0) {
        container.innerHTML = '';
        return;
    }
    let html = '<strong>File dipilih:</strong><ul style="margin: 6px 0 0 16px;">';
    for (let i = 0; i < input.files.length; i++) {
        html += '<li>' + input.files[i].name + ' (' + (input.files[i].size / 1024).toFixed(1) + ' KB)</li>';
    }
    html += '</ul>';
    container.innerHTML = html;
}
</script>
@endsection
