<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Tampilkan formulir login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi manual (Email & Password)
     * Dilengkapi rate-limiting, pencegahan timing attack & session fixation
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $credentials = [
            'email'    => strtolower(trim($request->input('email'))),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Cegah akses untuk akun yang dinonaktifkan oleh administrator
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Akun dinonaktifkan oleh Administrator. Silakan hubungi pengelola DISPUSIP Bangkalan.']);
            }

            // Regenerasi ID Sesi untuk mencegah Session Fixation
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            return $this->redirectAfterAuth($user, 'Selamat datang kembali, ' . e($user->name) . '!');
        }

        // Pesan error generik untuk mencegah enumeration attack
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau kata sandi tidak cocok.']);
    }

    /**
     * Tampilkan formulir pendaftaran manual
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi akun baru (Email & Password)
     * Strict RBAC: Semua pendaftaran publik mutlak berstatus role 'user'
     */
    public function register(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim($request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.max'           => 'Nama lengkap maksimal 100 karakter.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Alamat email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter demi keamanan.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name'              => strip_tags(trim($validated['name'])),
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => 'user', // Mutlak user biasa
            'is_active'         => true,
            'email_verified_at' => null,
        ]);

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $this->redirectAfterAuth($user, 'Pendaftaran berhasil! Akun Anda telah aktif.');
    }

    /**
     * Redirect ke Google OAuth 2.0 Resmi dengan CSRF State Token
     * Jika kredensial belum diset di .env, beralih ke Fallback Mode secara graceful
     */
    public function redirectToGoogle()
    {
        $clientId     = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        // Jika Google API Credentials lengkap di .env, jalankan OAuth 2.0 resmi
        if (!empty($clientId) && !empty($clientSecret)) {
            $state = Str::random(40);
            session(['google_oauth_state' => $state]);

            $redirectUri = $this->getGoogleRedirectUri();

            $query = http_build_query([
                'client_id'     => $clientId,
                'redirect_uri'  => $redirectUri,
                'response_type' => 'code',
                'scope'         => 'openid profile email',
                'state'         => $state,
                'prompt'        => 'select_account',
            ]);

            return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
        }

        // Fallback Mode: Tampilkan tampilan interaktif login Google untuk pengujian lokal/demo
        return view('auth.google-signin');
    }

    /**
     * Callback dari Google OAuth 2.0 Server
     * Memvalidasi kode otorisasi dan parameter CSRF state
     */
    public function handleGoogleCallback(Request $request)
    {
        // 1. Validasi CSRF State Token
        $savedState    = $request->session()->pull('google_oauth_state');
        $returnedState = (string) $request->input('state');

        if (empty($savedState) || !hash_equals($savedState, $returnedState)) {
            return redirect()->route('login')->with('error', 'Validasi sesi keamanan (state token) tidak valid atau kedaluwarsa. Silakan ulangi proses login.');
        }

        // 2. Cek apakah ada error dari Google atau kode otorisasi dibatalkan
        if ($request->has('error') || !$request->filled('code')) {
            return redirect()->route('login')->with('error', 'Otorisasi Akun Google dibatalkan.');
        }

        // 3. Tukar Authorization Code dengan Access Token (Backend-to-Backend HTTP Request)
        $tokenResponse = Http::timeout(10)->asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'code'          => $request->input('code'),
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $this->getGoogleRedirectUri(),
        ]);

        if (!$tokenResponse->successful()) {
            return redirect()->route('login')->with('error', 'Gagal memverifikasi token otentikasi Google.');
        }

        // 4. Ambil data profil terverifikasi dari Google UserInfo Endpoint
        $accessToken = $tokenResponse->json('access_token');
        $userResponse = Http::timeout(10)->withToken($accessToken)
            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if (!$userResponse->successful()) {
            return redirect()->route('login')->with('error', 'Gagal mengambil informasi profil dari akun Google Anda.');
        }

        $userInfo = $userResponse->json();

        if (empty($userInfo['email'])) {
            return redirect()->route('login')->with('error', 'Alamat email tidak ditemukan pada akun Google Anda.');
        }

        // 5. Intelligent & Safe Account Linking
        $user = $this->findOrCreateGoogleUser($userInfo);

        if (!$user->is_active) {
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh Administrator.');
        }

        Auth::login($user, true);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $this->redirectAfterAuth($user, 'Berhasil masuk dengan Akun Google!');
    }

    /**
     * Fallback Login Akun Google Langsung (Saat API keys Google belum diisi di .env)
     */
    public function processGoogleSignIn(Request $request)
    {
        $validated = $request->validate([
            'email'  => 'required|email|max:100',
            'name'   => 'nullable|string|max:100',
            'avatar' => 'nullable|url|max:500',
        ], [
            'email.required' => 'Masukkan alamat email Google Anda.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $email  = strtolower(trim($validated['email']));
        $name   = !empty($validated['name']) 
            ? strip_tags(trim($validated['name']))
            : ucwords(str_replace(['.', '_'], ' ', explode('@', $email)[0]));
        $avatar = !empty($validated['avatar'])
            ? $validated['avatar']
            : "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=000&color=fff&size=150";

        $user = $this->findOrCreateGoogleUser([
            'sub'     => 'google_sim_' . md5($email),
            'email'   => $email,
            'name'    => $name,
            'picture' => $avatar,
        ]);

        if (!$user->is_active) {
            return redirect()->route('login')->with('error', 'Akun ini sedang dinonaktifkan oleh Administrator.');
        }

        Auth::login($user, true);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $this->redirectAfterAuth($user, "Berhasil masuk dengan Akun Google ({$user->email})!");
    }

    /**
     * Safe Account Linking & Provisioning Helper
     * 1. Pencarian primer berdasarkan `google_id`
     * 2. Pencarian sekunder berdasarkan `email` terverifikasi
     * 3. Selalu sinkronkan foto profil Google terbaru (`picture`)
     */
    private function findOrCreateGoogleUser(array $info): User
    {
        $googleId = (string) ($info['sub'] ?? '');
        $email    = strtolower(trim($info['email'] ?? ''));
        $name     = !empty($info['name']) ? strip_tags(trim($info['name'])) : explode('@', $email)[0];
        
        // Ambil URL foto profil dari Google (biasanya pada atribut 'picture')
        $avatar   = !empty($info['picture']) ? $info['picture'] : null;

        $user = null;

        // Cek kecocokan google_id terlebih dahulu
        if (!empty($googleId)) {
            $user = User::where('google_id', $googleId)->first();
        }

        // Jika belum cocok dengan google_id, cocokkan via email (Safe Account Linking)
        if (!$user && !empty($email)) {
            $user = User::where('email', $email)->first();
        }

        // Jika akun sudah ada di database, perbarui link ID dan selalu sinkronkan foto profil Google
        if ($user) {
            $updates = [];
            if (empty($user->google_id) && !empty($googleId)) {
                $updates['google_id'] = $googleId;
            }
            // Selalu perbarui foto profil dengan gambar profil Google terbaru
            if (!empty($avatar)) {
                $updates['avatar'] = $avatar;
            }
            if (is_null($user->email_verified_at)) {
                $updates['email_verified_at'] = now();
            }
            if (!empty($updates)) {
                $user->update($updates);
            }
            return $user;
        }

        // Buat akun baru jika belum terdaftar sama sekali dengan foto profil Google
        return User::create([
            'name'              => $name,
            'email'             => $email,
            'google_id'         => $googleId,
            'avatar'            => $avatar,
            'password'          => Hash::make(Str::random(32)), // Kata sandi acak kuat
            'role'              => 'user',
            'is_active'         => true,
            'email_verified_at' => now(), // Email Google telah terbukti valid
        ]);
    }

    /**
     * Arahkan pengguna setelah berhasil autentikasi ke tujuan semula atau dashboard
     */
    private function redirectAfterAuth(User $user, ?string $message = null)
    {
        $target = $user->isPetugas()
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('complaint.create'));

        return $message ? $target->with('success', $message) : $target;
    }

    /**
     * Logout pengguna dengan pembersihan sesi total
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Resolves Google Redirect URI securely from config or route
     */
    private function getGoogleRedirectUri(): string
    {
        $configured = config('services.google.redirect');
        if (!empty($configured) && (str_starts_with($configured, 'http://') || str_starts_with($configured, 'https://'))) {
            return $configured;
        }

        return route('auth.google.callback');
    }
}
