<?php

namespace Tests\Feature;

use App\Livewire\Admin\Users\AdminUserManager;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAuthAndRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $student;

    protected Role $superAdminRole;

    protected Role $adminRole;

    protected Role $studentRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingSeeder::class);

        $this->superAdminRole = Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
        $this->adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $this->studentRole = Role::firstOrCreate(['name' => 'STUDENT']);

        $this->superAdmin = User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@rgplacademy.com',
            'mobile_number' => '9999900001',
            'password' => Hash::make('SuperSecurePass123!'),
        ]);
        $this->superAdmin->roles()->attach($this->superAdminRole->id);

        $this->admin = User::factory()->create([
            'name' => 'Staff Administrator',
            'email' => 'admin@rgplacademy.com',
            'mobile_number' => '9999900002',
            'password' => Hash::make('AdminPass123!'),
        ]);
        $this->admin->roles()->attach($this->adminRole->id);

        $this->student = User::factory()->create([
            'name' => 'Regular Student',
            'email' => 'student@example.com',
            'mobile_number' => '9888800001',
            'password' => Hash::make('StudentPass123!'),
        ]);
        $this->student->roles()->attach($this->studentRole->id);
    }

    public function test_admin_route_redirects_unauthenticated_user_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_route_redirects_authenticated_admins_to_admin_dashboard(): void
    {
        // Super Admin
        $this->actingAs($this->superAdmin);
        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.dashboard'));

        // Admin
        $this->actingAs($this->admin);
        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_route_redirects_regular_student_to_admin_login(): void
    {
        $this->actingAs($this->student);

        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_page_renders_with_rgpl_branding_and_no_self_registration(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('RGPL');
        $response->assertSee('Academy');
        $response->assertSee('Admin Login');
        $response->assertSee('Sign In to Admin Panel');
        $response->assertSee('Authorized Administrative Access Only');

        // Verify no registration / signup options
        $response->assertDontSee('Register');
        $response->assertDontSee('Create Account');
        $response->assertDontSee('Sign up');
        $response->assertDontSee('Register here');

        // Verify no legacy ParikshaMitra branding or Marathi text
        $response->assertDontSee('ParikshaMitra');
        $response->assertDontSee('परीक्षा');
    }

    public function test_super_admin_can_authenticate_via_admin_login(): void
    {
        $response = $this->post(route('admin.login.store'), [
            'login' => 'superadmin@rgplacademy.com',
            'password' => 'SuperSecurePass123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->superAdmin);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'LOGIN',
            'module' => 'AUTH',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_admin_can_authenticate_via_mobile_number(): void
    {
        $response = $this->post(route('admin.login.store'), [
            'login' => '9999900002',
            'password' => 'AdminPass123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'LOGIN',
            'module' => 'AUTH',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_regular_student_cannot_log_in_via_admin_login(): void
    {
        $response = $this->post(route('admin.login.store'), [
            'login' => 'student@example.com',
            'password' => 'StudentPass123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['login']);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'UNAUTHORIZED_ADMIN_LOGIN',
            'module' => 'AUTH',
            'user_id' => $this->student->id,
        ]);
    }

    public function test_admin_login_rejects_invalid_password(): void
    {
        $response = $this->post(route('admin.login.store'), [
            'login' => 'superadmin@rgplacademy.com',
            'password' => 'WrongPassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['login']);
    }

    public function test_admin_can_request_otp_with_registered_mobile_number(): void
    {
        $response = $this->post(route('admin.login.send-otp'), [
            'mobile_number' => '9999900001',
        ]);

        $response->assertRedirect(route('admin.login', ['tab' => 'otp']));
        $response->assertSessionHas('admin_otp_mobile', '9999900001');
        $this->assertDatabaseHas('otp_verifications', [
            'mobile_number' => '9999900001',
        ]);
    }

    public function test_admin_can_verify_otp_and_authenticate(): void
    {
        // First request OTP
        $this->post(route('admin.login.send-otp'), [
            'mobile_number' => '9999900001',
        ]);

        $otp = session('admin_dev_otp');
        $this->assertNotNull($otp);

        $response = $this->post(route('admin.login.verify-otp'), [
            'mobile_number' => '9999900001',
            'otp' => $otp,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->superAdmin);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'LOGIN',
            'module' => 'AUTH',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_non_admin_cannot_request_admin_otp(): void
    {
        $response = $this->post(route('admin.login.send-otp'), [
            'mobile_number' => '9888800001', // student mobile
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['mobile_number']);
        $this->assertDatabaseMissing('otp_verifications', [
            'mobile_number' => '9888800001',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'UNAUTHORIZED_ADMIN_OTP_REQUEST',
            'module' => 'AUTH',
        ]);
    }

    public function test_admin_cannot_login_with_invalid_otp(): void
    {
        $this->post(route('admin.login.send-otp'), [
            'mobile_number' => '9999900001',
        ]);

        $response = $this->post(route('admin.login.verify-otp'), [
            'mobile_number' => '9999900001',
            'otp' => '000000',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['otp']);
    }

    public function test_admin_can_logout_via_admin_logout_route(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post(route('admin.logout'));

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'LOGOUT',
            'module' => 'AUTH',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_super_admin_can_access_admin_users_and_create_new_admin(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);

        Livewire::test(AdminUserManager::class)
            ->set('name', 'Course Coordinator')
            ->set('email', 'coordinator@rgplacademy.com')
            ->set('mobile_number', '9888877777')
            ->set('role', 'ADMIN')
            ->set('password', 'CoordinatorPass123!')
            ->call('save')
            ->assertHasNoErrors();

        $newAdmin = User::where('email', 'coordinator@rgplacademy.com')->first();
        $this->assertNotNull($newAdmin);
        $this->assertTrue($newAdmin->hasRole('ADMIN'));
        $this->assertFalse($newAdmin->hasRole('SUPER_ADMIN'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'CREATE',
            'module' => 'ADMIN_USERS',
            'user_id' => $this->superAdmin->id,
            'record_id' => (string) $newAdmin->id,
        ]);

        // Verify newly created admin can log in
        $this->post(route('admin.logout'));

        $loginResponse = $this->post(route('admin.login.store'), [
            'login' => 'coordinator@rgplacademy.com',
            'password' => 'CoordinatorPass123!',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($newAdmin);
    }

    public function test_non_super_admin_cannot_access_admin_users_management(): void
    {
        // Regular Admin cannot access admin user management
        $this->actingAs($this->admin);
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(403);

        // Student cannot access admin user management
        $this->actingAs($this->student);
        $studentResponse = $this->get(route('admin.users.index'));
        $studentResponse->assertStatus(403);
    }

    public function test_student_cannot_access_any_admin_panel_route(): void
    {
        $this->actingAs($this->student);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $coursesResponse = $this->get(route('admin.courses.index'));
        $coursesResponse->assertStatus(403);
    }

    public function test_super_admin_can_reset_admin_password(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(AdminUserManager::class)
            ->call('openPasswordModal', $this->admin->id)
            ->set('newPassword', 'NewAdminPassword456!')
            ->call('resetPassword')
            ->assertHasNoErrors();

        $this->admin->refresh();
        $this->assertTrue(Hash::check('NewAdminPassword456!', $this->admin->password));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'RESET_PASSWORD',
            'module' => 'ADMIN_USERS',
            'record_id' => (string) $this->admin->id,
        ]);
    }

    public function test_super_admin_cannot_delete_their_own_account(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(AdminUserManager::class)
            ->call('deleteUser', $this->superAdmin->id);

        $this->assertDatabaseHas('users', [
            'id' => $this->superAdmin->id,
        ]);
    }
}
