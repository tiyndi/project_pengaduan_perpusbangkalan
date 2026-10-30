<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_dashboard()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create([
            'role'      => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin');
        // Route middleware 'petugas' should block non-staff
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    public function test_admin_can_access_dashboard_and_render_monthly_stats_without_sql_errors()
    {
        $admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name'  => 'Pelayanan',
            'slug'  => 'pelayanan',
            'color' => '#1a56db',
            'icon'  => 'bi-chat-dots',
        ]);

        Complaint::create([
            'tracking_code'  => 'TRK-000001',
            'subject'        => 'Contoh Keluhan Uji Coba',
            'description'    => 'Deskripsi keluhan untuk pengujian dashboard admin.',
            'category_id'    => $category->id,
            'status'         => 'resolved',
            'reporter_name'  => 'Udin Test',
            'reporter_email' => 'udin@example.com',
            'reporter_phone' => '08123456789',
        ]);

        $this->withoutExceptionHandling();
        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('chartData');
        $response->assertViewHas('stats');
    }
}
