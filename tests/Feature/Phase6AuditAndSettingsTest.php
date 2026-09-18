<?php

namespace Tests\Feature;

use App\Livewire\Admin\AuditLogs\AuditLogIndex;
use App\Livewire\Admin\Payments\PaymentIndex;
use App\Livewire\Admin\Settings\SettingsManager;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Phase6AuditAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingSeeder::class);

        $this->student = User::factory()->create([
            'name' => 'John Student',
            'mobile_number' => '9111122222',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'mobile_number' => '9888899999',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
        $this->admin->roles()->attach($adminRole->id);
    }

    public function test_setting_get_and_set_with_cache_invalidation(): void
    {
        $freeQuota = Setting::get('default_free_limit');
        $this->assertEquals(500, $freeQuota);
        $this->assertIsInt($freeQuota);

        Setting::set('default_free_limit', 750, 'practice', 'integer');
        $updated = Setting::get('default_free_limit');
        $this->assertEquals(750, $updated);

        $siteName = Setting::get('site_name');
        $this->assertEquals('Online MCQ Practice & Examination Platform', $siteName);
    }

    public function test_audit_log_records_correctly(): void
    {
        $this->actingAs($this->admin);

        AuditLog::record(
            action: 'CUSTOM_TEST_ACTION',
            module: 'TEST_MODULE',
            recordId: 'REC-123',
            oldValues: ['status' => 'old'],
            newValues: ['status' => 'new']
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'CUSTOM_TEST_ACTION',
            'module' => 'TEST_MODULE',
            'record_id' => 'REC-123',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_payment_approval_generates_audit_log(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Crash Course Pass',
            'price' => 399.00,
            'duration_days' => 30,
            'is_all_courses' => true,
            'is_active' => true,
        ]);

        $payment = Payment::create([
            'user_id' => $this->student->id,
            'subscription_plan_id' => $plan->id,
            'amount' => 399.00,
            'payment_reference' => 'UTR1234567890',
            'status' => 'PENDING',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(PaymentIndex::class)
            ->call('approvePayment', $payment->id);

        $payment->refresh();
        $this->assertEquals('APPROVED', $payment->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'PAYMENT_APPROVED',
            'module' => 'PAYMENTS',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_settings_manager_updates_settings_and_logs_audit(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SettingsManager::class)
            ->set('site_name', 'Maharashtra Exams Portal')
            ->set('contact_email', 'help@mpscportal.com')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('Maharashtra Exams Portal', Setting::get('site_name'));
        $this->assertEquals('help@mpscportal.com', Setting::get('contact_email'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'SETTINGS_UPDATED',
            'module' => 'SETTINGS',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_audit_logs_viewer_renders_and_filters(): void
    {
        $this->actingAs($this->admin);

        AuditLog::record(action: 'LOGIN', module: 'AUTH', recordId: 'USER-1');
        AuditLog::record(action: 'QUESTION_UPDATE', module: 'CONTENT', recordId: 'Q-99');

        Livewire::test(AuditLogIndex::class)
            ->assertSee('All Modules')
            ->set('moduleFilter', 'AUTH')
            ->assertSee('LOGIN')
            ->assertDontSee('QUESTION_UPDATE');
    }

    public function test_admin_dashboard_metrics_render_properly(): void
    {
        $this->actingAs($this->admin);

        Course::create([
            'name' => 'Civil Engineering Prelims',
            'is_active' => true,
            'free_question_limit' => 500,
        ]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Executive Administration &amp; Analytics', false);
        $response->assertSee('Approved Revenue');
        $response->assertSee('Total Students');
    }

    public function test_non_admin_cannot_access_audit_logs_or_settings(): void
    {
        $this->actingAs($this->student);

        $this->get(route('admin.audit-logs.index'))->assertStatus(403);
        $this->get(route('admin.settings.index'))->assertStatus(403);
        $this->get(route('admin.dashboard'))->assertStatus(403);
    }
}
