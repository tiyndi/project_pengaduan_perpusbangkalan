@extends('layouts.app')

@section('title', 'Masuk dengan Google')

@section('content')
<div style="min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center; padding: 40px 16px;">
    <div style="width: 100%; max-width: 450px; background: #ffffff; border: 1px solid #dadce0; border-radius: 16px; padding: 40px 36px; box-shadow: 0 4px 18px rgba(60, 64, 67, 0.08);">
        <!-- Logo Google Resmi -->
        <div style="text-align: center; margin-bottom: 24px;">
            <svg viewBox="0 0 24 24" width="44" height="44" style="display: inline-block;">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <h1 style="font-size: 22px; font-weight: 600; color: #202124; margin: 16px 0 6px; letter-spacing: -0.2px;">Masuk dengan Google</h1>
            <p style="font-size: 13.5px; color: #5f6368; margin: 0;">Lanjutkan ke <strong>Pengaduan DISPUSIP Bangkalan</strong></p>
        </div>

        <!-- Form Masuk Akun Google -->
        <form action="{{ route('auth.google.signin') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="google_email" style="display: block; font-size: 12.5px; font-weight: 600; color: #3c4043; margin-bottom: 6px;">
                    Email Google (Gmail)
                </label>
                <div style="position: relative;">
                    <i class="bi bi-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #5f6368; font-size: 16px;"></i>
                    <input type="email" id="google_email" name="email" value="{{ old('email', 'liana.safitri@gmail.com') }}" required autofocus
                           placeholder="nama@gmail.com" 
                           style="width: 100%; padding: 12px 14px 12px 42px; border: 1px solid #dadce0; border-radius: 8px; font-size: 14px; color: #202124; font-family: inherit; outline: none; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#1a73e8'; this.style.boxShadow='0 0 0 2px rgba(26,115,232,0.2)';"
                           onblur="this.style.borderColor='#dadce0'; this.style.boxShadow='none';">
                </div>
                @error('email')
                    <span style="display: block; font-size: 12px; color: #d93025; margin-top: 5px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 18px;">
                <label for="google_name" style="display: block; font-size: 12.5px; font-weight: 600; color: #3c4043; margin-bottom: 6px;">
                    Nama Profil Google (Opsional)
                </label>
                <input type="text" id="google_name" name="name" value="{{ old('name', 'Liana Safitri') }}"
                       placeholder="Nama lengkap Anda di Google"
                       style="width: 100%; padding: 11px 14px; border: 1px solid #dadce0; border-radius: 8px; font-size: 13.5px; color: #202124; font-family: inherit; outline: none; box-sizing: border-box;"
                       onfocus="this.style.borderColor='#1a73e8'; this.style.boxShadow='0 0 0 2px rgba(26,115,232,0.2)';"
                       onblur="this.style.borderColor='#dadce0'; this.style.boxShadow='none';">
            </div>

            <div style="margin-bottom: 24px;">
                <label for="google_avatar" style="display: block; font-size: 12.5px; font-weight: 600; color: #3c4043; margin-bottom: 6px;">
                    URL Foto Profil Google (Opsional)
                </label>
                <input type="url" id="google_avatar" name="avatar" value="{{ old('avatar', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80') }}"
                       placeholder="https://..."
                       style="width: 100%; padding: 11px 14px; border: 1px solid #dadce0; border-radius: 8px; font-size: 13.5px; color: #202124; font-family: inherit; outline: none; box-sizing: border-box;"
                       onfocus="this.style.borderColor='#1a73e8'; this.style.boxShadow='0 0 0 2px rgba(26,115,232,0.2)';"
                       onblur="this.style.borderColor='#dadce0'; this.style.boxShadow='none';">
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px;">
                <a href="{{ route('login') }}" style="font-size: 13px; font-weight: 600; color: #1a73e8; text-decoration: none;">
                    Batal
                </a>
                <button type="submit" 
                        style="background: #1a73e8; color: #ffffff; border: none; padding: 11px 26px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.15s; box-shadow: 0 1px 3px rgba(60,64,67,0.3);"
                        onmouseover="this.style.background='#1557b0';"
                        onmouseout="this.style.background='#1a73e8';">
                    Lanjutkan <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </form>

        <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #f1f3f4; font-size: 11.5px; color: #70757a; text-align: center; line-height: 1.5;">
            Dengan melanjutkan, Google akan membagikan nama, alamat email, dan foto profil Anda kepada pengelola <strong>Pengaduan DISPUSIP Bangkalan</strong>.
        </div>
    </div>
</div>
@endsection
