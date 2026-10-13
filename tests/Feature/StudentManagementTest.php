<?php

namespace Tests\Feature;

use App\Livewire\Admin\Students\StudentManager;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'SUPER_ADMIN'], ['description' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'ADMIN'], ['description' => 'Admin']);
        Role::firstOrCreate(['name' => 'STUDENT'], ['description' => 'Student']);
    }

    public function test_admin_can_access_student_manager_page(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'ADMIN')->first());

        $student = User::factory()->create([
            'name' => 'Test Student',
            'mobile_number' => '9876543210',
            'is_active' => true,
        ]);
        $student->roles()->attach(Role::where('name', 'STUDENT')->first());

        $response = $this->actingAs($admin)->get(route('admin.students.index'));

        $response->assertStatus(200);
        $response->assertSee('Student Accounts & Activity');
        $response->assertSee('Test Student');
    }

    public function test_non_admin_cannot_access_student_manager_page(): void
    {
        // Unauthenticated
        $this->get(route('admin.students.index'))->assertRedirect(route('login'));

        // Student role
        $student = User::factory()->create();
        $student->roles()->attach(Role::where('name', 'STUDENT')->first());

        $this->actingAs($student)->get(route('admin.students.index'))->assertStatus(403);
    }

    public function test_admin_can_toggle_student_active_status_and_logs_audit(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'ADMIN')->first());

        $student = User::factory()->create([
            'name' => 'Aarav Patel',
            'mobile_number' => '9898989898',
            'is_active' => true,
        ]);
        $student->roles()->attach(Role::where('name', 'STUDENT')->first());

        Livewire::actingAs($admin)
            ->test(StudentManager::class)
            ->assertSee('Aarav Patel')
            ->call('toggleStatus', $student->id)
            ->assertSee('has been deactivated successfully');

        $this->assertFalse($student->fresh()->is_active);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'DEACTIVATE_STUDENT',
            'module' => 'STUDENTS',
            'record_id' => (string) $student->id,
            'user_id' => $admin->id,
        ]);

        // Toggle back to active
        Livewire::actingAs($admin)
            ->test(StudentManager::class)
            ->call('toggleStatus', $student->id);

        $this->assertTrue($student->fresh()->is_active);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'ACTIVATE_STUDENT',
            'module' => 'STUDENTS',
            'record_id' => (string) $student->id,
            'user_id' => $admin->id,
        ]);
    }

    public function test_deactivated_student_cannot_log_in(): void
    {
        $student = User::factory()->create([
            'name' => 'Inactive User',
            'mobile_number' => '9111122222',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);
        $student->roles()->attach(Role::where('name', 'STUDENT')->first());

        $response = $this->post(route('login'), [
            'mobile_number' => '9111122222',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['mobile_number']);
    }

    public function test_deactivated_student_session_is_invalidated_by_middleware(): void
    {
        $student = User::factory()->create([
            'name' => 'Deactivated Student',
            'mobile_number' => '9333344444',
            'is_active' => false,
        ]);
        $student->roles()->attach(Role::where('name', 'STUDENT')->first());

        $response = $this->actingAs($student)->get(route('dashboard'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['mobile_number']);
    }

    public function test_admin_dashboard_displays_student_counts_and_activity(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'ADMIN')->first());

        $activeStudent = User::factory()->create(['name' => 'Active One', 'is_active' => true]);
        $activeStudent->roles()->attach(Role::where('name', 'STUDENT')->first());

        $inactiveStudent = User::factory()->create(['name' => 'Inactive One', 'is_active' => false]);
        $inactiveStudent->roles()->attach(Role::where('name', 'STUDENT')->first());

        AuditLog::record(
            action: 'LOGIN',
            module: 'AUTH',
            recordId: (string) $activeStudent->id,
            userId: $activeStudent->id
        );

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('1 active');
        $response->assertSee('1 inactive');
        $response->assertSee('Student Accounts &amp; Quota Central', false);
        $response->assertSee('Active One');
    }
}
