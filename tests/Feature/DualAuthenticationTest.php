<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DualAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Lanjutkan dengan Google');
        $response->assertSee('Masuk');
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Cepat dengan Google');
        $response->assertSee('Pendaftaran Akun');
    }

    public function test_new_user_can_register_manually()
    {
        $response = $this->post('/register', [
            'name'                  => 'Warga Bangkalan Baru',
            'email'                 => 'warga.baru@bangkalan.id',
            'password'              => 'PasswordKuat2026!',
            'password_confirmation' => 'PasswordKuat2026!',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email'     => 'warga.baru@bangkalan.id',
            'role'      => 'user',
            'is_active' => true,
        ]);
        $response->assertRedirect('/laporan/buat');
    }

    public function test_registration_fails_if_password_under_8_characters()
    {
        $response = $this->post('/register', [
            'name'                  => 'Warga Pendek',
            'email'                 => 'warga.pendek@bangkalan.id',
            'password'              => 'pass1',
            'password_confirmation' => 'pass1',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_strictly_sets_role_to_user_preventing_escalation()
    {
        $response = $this->post('/register', [
            'name'                  => 'Penyerang Hacker',
            'email'                 => 'attacker@bangkalan.id',
            'password'              => 'PasswordKuat2026!',
            'password_confirmation' => 'PasswordKuat2026!',
            'role'                  => 'admin', // Coba injeksi role admin
        ]);

        $this->assertAuthenticated();
        $user = User::where('email', 'attacker@bangkalan.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role, 'Role harus tetap user meskipun diinjeksi admin!');
    }

    public function test_users_can_authenticate_using_valid_credentials()
    {
        $user = User::create([
            'name'      => 'Pengguna Uji',
            'email'     => 'pengguna.uji@bangkalan.id',
            'password'  => Hash::make('SandiRahasia123!'),
            'role'      => 'user',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => 'pengguna.uji@bangkalan.id',
            'password' => 'SandiRahasia123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/laporan/buat');
    }

    public function test_users_cannot_authenticate_with_invalid_password()
    {
        $user = User::create([
            'name'      => 'Pengguna Uji',
            'email'     => 'pengguna.uji@bangkalan.id',
            'password'  => Hash::make('SandiRahasia123!'),
            'role'      => 'user',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => 'pengguna.uji@bangkalan.id',
            'password' => 'SandiSalah!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_users_are_forbidden_from_logging_in()
    {
        $inactiveUser = User::create([
            'name'      => 'Pengguna Nonaktif',
            'email'     => 'nonaktif@bangkalan.id',
            'password'  => Hash::make('SandiRahasia123!'),
            'role'      => 'user',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email'    => 'nonaktif@bangkalan.id',
            'password' => 'SandiRahasia123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_google_redirect_route_is_accessible()
    {
        $response = $this->get('/auth/google');
        // Saat client_id belum diset di .env, akan menampilkan fallback Google Sign-In view (200)
        // Jika client_id diset, akan redirect 302 ke accounts.google.com
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }

    public function test_google_signin_auto_links_with_existing_manual_email_without_duplicate()
    {
        // 1. User mendaftar manual terlebih dahulu
        $existing = User::create([
            'name'      => 'Dewi Lestari Manual',
            'email'     => 'dewi.lestari@gmail.com',
            'password'  => Hash::make('SandiLama2024!'),
            'role'      => 'user',
            'is_active' => true,
        ]);

        // 2. Kemudian masuk lewat Google Sign-in dengan email yang sama
        $response = $this->post('/auth/google/signin', [
            'email' => 'dewi.lestari@gmail.com',
            'name'  => 'Dewi Lestari Google',
        ]);

        $this->assertAuthenticated();
        
        // 3. Pastikan tidak ada duplikasi akun
        $count = User::where('email', 'dewi.lestari@gmail.com')->count();
        $this->assertEquals(1, $count, 'Jumlah akun dengan email yang sama harus tetap 1 (auto-linked)!');

        $updated = User::where('email', 'dewi.lestari@gmail.com')->first();
        $this->assertNotNull($updated->google_id, 'Kolom google_id harus tertaut otomatis!');
    }

    public function test_google_signin_saves_and_updates_profile_picture_avatar()
    {
        $googlePhotoUrl = 'https://lh3.googleusercontent.com/a/ACg8ocL_sample_photo=s96-c';

        $response = $this->post('/auth/google/signin', [
            'email'  => 'google.user.avatar@gmail.com',
            'name'   => 'Google User Avatar',
            'avatar' => $googlePhotoUrl,
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'google.user.avatar@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals($googlePhotoUrl, $user->avatar, 'Avatar di database harus menyimpan URL foto Google!');
        $this->assertEquals($googlePhotoUrl, $user->avatar_url, 'Accessor avatar_url harus mengembalikan URL foto Google!');
    }

    public function test_user_can_logout_securely()
    {
        $user = User::create([
            'name'      => 'Pengguna Logout',
            'email'     => 'user.logout@bangkalan.id',
            'password'  => Hash::make('SandiRahasia123!'),
            'role'      => 'user',
            'is_active' => true,
        ]);

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
