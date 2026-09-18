<?php

namespace Tests\Feature;

use App\Livewire\Admin\Payments\PaymentIndex;
use App\Livewire\Student\Subscriptions\PlanList;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $admin;

    protected Course $course;

    protected SubscriptionPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->student = User::factory()->create([
            'mobile_number' => '9888877777',
        ]);

        $this->admin = User::factory()->create([
            'mobile_number' => '9999988888',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
        $this->admin->roles()->attach($adminRole->id);

        $this->course = Course::create([
            'name' => 'MPSC Prelims Course',
            'is_active' => true,
            'free_question_limit' => 500,
        ]);

        $this->plan = SubscriptionPlan::create([
            'name' => 'MPSC 90-Day Pass',
            'price' => 499.00,
            'duration_days' => 90,
            'is_all_courses' => false,
            'is_active' => true,
        ]);
        $this->plan->courses()->attach($this->course->id);
    }

    public function test_student_can_view_subscription_plans(): void
    {
        $this->actingAs($this->student)
            ->get(route('student.plans'))
            ->assertOk()
            ->assertSee('MPSC 90-Day Pass')
            ->assertSee('499');
    }

    public function test_student_can_submit_manual_payment_receipt(): void
    {
        $fakeFile = UploadedFile::fake()->image('receipt.png');

        Livewire::actingAs($this->student)
            ->test(PlanList::class)
            ->call('selectPlan', $this->plan->id)
            ->assertSet('selectedPlanId', $this->plan->id)
            ->assertSet('showPaymentModal', true)
            ->set('payment_method', 'UPI')
            ->set('transaction_reference', 'UPI1234567890')
            ->set('receipt', $fakeFile)
            ->set('student_notes', 'Paid via GooglePay')
            ->call('submitPayment')
            ->assertRedirect(route('student.subscriptions'));

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->student->id,
            'subscription_plan_id' => $this->plan->id,
            'amount' => 499.00,
            'payment_method' => 'UPI',
            'transaction_reference' => 'UPI1234567890',
            'status' => 'PENDING',
        ]);
    }

    public function test_admin_can_approve_payment_and_activate_subscription(): void
    {
        $payment = Payment::create([
            'user_id' => $this->student->id,
            'subscription_plan_id' => $this->plan->id,
            'amount' => 499.00,
            'payment_method' => 'UPI',
            'transaction_reference' => 'UTR987654321',
            'receipt_path' => 'receipts/fake.png',
            'status' => 'PENDING',
        ]);

        // Student has NO active subscription yet
        $this->assertFalse($this->student->hasActiveSubscription($this->course->id));

        // Admin approves payment
        Livewire::actingAs($this->admin)
            ->test(PaymentIndex::class)
            ->call('approvePayment', $payment->id);

        // Payment status becomes APPROVED
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'APPROVED',
            'reviewed_by' => $this->admin->id,
        ]);

        // Subscription record is generated
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->student->id,
            'subscription_plan_id' => $this->plan->id,
            'payment_id' => $payment->id,
            'status' => 'ACTIVE',
        ]);

        // Student now HAS an active subscription for this course!
        $this->assertTrue($this->student->fresh()->hasActiveSubscription($this->course->id));

        // And free quota limit is bypassed (unlimited practice!)
        $this->assertFalse($this->student->fresh()->hasExceededFreeQuota($this->course->id));
    }

    public function test_admin_can_reject_payment_with_reason(): void
    {
        $payment = Payment::create([
            'user_id' => $this->student->id,
            'subscription_plan_id' => $this->plan->id,
            'amount' => 499.00,
            'payment_method' => 'UPI',
            'transaction_reference' => 'INVALID_UTR',
            'status' => 'PENDING',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PaymentIndex::class)
            ->call('openRejectModal', $payment->id)
            ->set('rejectionReason', 'Invalid transaction reference, money not received.')
            ->call('rejectPayment');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'REJECTED',
            'rejection_reason' => 'Invalid transaction reference, money not received.',
            'reviewed_by' => $this->admin->id,
        ]);

        $this->assertFalse($this->student->fresh()->hasActiveSubscription($this->course->id));
    }

    public function test_student_can_view_active_subscriptions_and_history(): void
    {
        Subscription::create([
            'user_id' => $this->student->id,
            'subscription_plan_id' => $this->plan->id,
            'starts_at' => now(),
            'expires_at' => now()->addDays(90),
            'status' => 'ACTIVE',
        ]);

        Payment::create([
            'user_id' => $this->student->id,
            'subscription_plan_id' => $this->plan->id,
            'amount' => 499.00,
            'payment_method' => 'UPI',
            'transaction_reference' => 'UTR112233',
            'status' => 'APPROVED',
        ]);

        $this->actingAs($this->student)
            ->get(route('student.subscriptions'))
            ->assertOk()
            ->assertSee('MPSC 90-Day Pass')
            ->assertSee('UTR112233')
            ->assertSee('APPROVED');
    }
}
