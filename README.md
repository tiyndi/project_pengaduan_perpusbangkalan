# Pengaduan DISPUSIP Bangkalan

Aplikasi web layanan pengaduan dan aspirasi masyarakat untuk **Dinas Perpustakaan dan Kearsipan (DISPUSIP) Kabupaten Bangkalan** berbasis Laravel.

---

## Fitur Utama

1. **Autentikasi Cepat & Fleksibel**
   - **Login & Registrasi Langsung via Google (OAuth 2.0)**: Pengguna dapat masuk atau mendaftar cukup dengan satu klik akun Google mereka.
   - **Registrasi & Login Manual**: Menggunakan nama, email, dan kata sandi dengan validasi keamanan.
   - **Tombol Cepat Akun Demo**: Memudahkan pengujian akun Admin dan Petugas langsung di halaman login.

2. **Layanan Pengaduan & Aspirasi**
   - Formulir pengaduan dengan unggah lampiran bukti foto/dokumen.
   - Penomoran tiket aduan otomatis (`ADU-XXXX`).
   - Lacak status tindak lanjut aduan secara real-time.

3. **Panel Admin & Petugas**
   - Dashboard statistik aduan (Total, Menunggu, Diproses, Selesai, Ditolak).
   - Pengelolaan kategori pengaduan.
   - Respon resmi dan update status tiket kepada pelapor.

---

## Konfigurasi Google OAuth

Untuk mengaktifkan login langsung dengan Google, tambahkan kredensial dari **Google Cloud Console** ke file `.env`:

```env
# Google OAuth 2.0
GOOGLE_CLIENT_ID=isi_client_id_google_anda
GOOGLE_CLIENT_SECRET=isi_client_secret_google_anda
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

> **Catatan Pengujian:** Jika kredensial belum diisi, sistem tetap aman dan akan menampilkan notifikasi informatif ketika tombol Google diklik, tanpa menyebabkan crash/error server.

---

## Cara Menjalankan

1. **Jalankan Apache & Database (MySQL)** di XAMPP / Laragon / sistem Anda.
2. **Migrasi Database & Seeder**:
   ```bash
   php artisan migrate --seed
   ```
   *(Atau impor file `database/database_mysql.sql` secara langsung ke MySQL/phpMyAdmin)*
3. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di peramban: `http://localhost:8000`

---

## Akun Bawaan (Default Demo)

| Peran | Email | Kata Sandi |
|---|---|---|
| **Administrator** | `admin@perpusbangkalan.go.id` | `Admin@Perpus2024!` |
| **Petugas** | `petugas@perpusbangkalan.go.id` | `Petugas@2024!` |
