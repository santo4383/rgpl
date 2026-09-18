<?php

namespace Tests\Feature\Api;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected SubscriptionPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create(['mobile_number' => '9876543210']);

        $this->plan = SubscriptionPlan::create([
            'name' => 'All-Access Annual Pass',
            'description' => 'Unlimited practice and mock tests across all streams',
            'price' => 999.00,
            'duration_days' => 365,
            'is_all_courses' => true,
            'is_active' => true,
        ]);
    }

    public function test_payment_instructions_returns_dynamic_bank_details(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/payments/instructions?plan_id={$this->plan->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'plan' => [
                        'id' => $this->plan->id,
                        'price' => 999.00,
                    ],
                ],
            ]);

        $this->assertNotEmpty($response->json('data.upi_id'));
        $this->assertNotEmpty($response->json('data.account_number'));
    }

    public function test_submit_manual_payment_with_multipart_receipt(): void
    {
        Sanctum::actingAs($this->user);

        $file = UploadedFile::fake()->create('receipt.png', 500, 'image/png');

        $response = $this->postJson('/api/v1/payments/submit', [
            'subscription_plan_id' => $this->plan->id,
            'transaction_reference' => 'UTR1234567890',
            'receipt' => $file,
            'student_notes' => 'Paid via GooglePay',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'PENDING',
                    'transaction_reference' => 'UTR1234567890',
                    'amount' => 999.00,
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'transaction_reference' => 'UTR1234567890',
            'status' => 'PENDING',
        ]);
    }
}
