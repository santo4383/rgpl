<?php

namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\User;
use App\Services\SMS\SmsProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Bind mock SMS provider
        $this->app->bind(SmsProviderInterface::class, function () {
            return new class implements SmsProviderInterface
            {
                public function send(string $mobileNumber, string $message): bool
                {
                    return true;
                }
            };
        });
    }

    public function test_send_login_otp_succeeds_for_registered_user(): void
    {
        $user = User::factory()->create(['mobile_number' => '9876543210']);

        $response = $this->postJson('/api/v1/auth/login/send-otp', [
            'mobile_number' => '9876543210',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['mobile_number' => '9876543210'],
            ]);

        $this->assertDatabaseHas('otp_verifications', [
            'mobile_number' => '9876543210',
            'is_verified' => false,
        ]);
    }

    public function test_verify_login_otp_returns_bearer_token(): void
    {
        $user = User::factory()->create(['mobile_number' => '9876543210']);

        $this->postJson('/api/v1/auth/login/send-otp', ['mobile_number' => '9876543210']);

        // Set a known hash for verification
        DB::table('otp_verifications')->where('mobile_number', '9876543210')->update([
            'otp_hash' => Hash::make('123456'),
        ]);

        $response = $this->postJson('/api/v1/auth/login/verify', [
            'mobile_number' => '9876543210',
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'mobile_number'],
                ],
            ]);
    }

    public function test_registration_flow_with_multiple_streams(): void
    {
        $mpsc = Course::create(['name' => 'MPSC', 'is_active' => true]);
        $commerce = Course::create(['name' => 'Commerce', 'is_active' => true]);

        // 1. Send register OTP
        $response = $this->postJson('/api/v1/auth/register/send-otp', [
            'name' => 'Rahul Patil',
            'mobile_number' => '9123456780',
            'dob' => '2000-01-15',
            'address' => 'FC Road, Pune',
            'course_ids' => [$mpsc->id, $commerce->id],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        // Mock known OTP
        DB::table('otp_verifications')->where('mobile_number', '9123456780')->update([
            'otp_hash' => Hash::make('654321'),
        ]);

        // 2. Verify register OTP
        $verifyRes = $this->postJson('/api/v1/auth/register/verify', [
            'name' => 'Rahul Patil',
            'mobile_number' => '9123456780',
            'dob' => '2000-01-15',
            'address' => 'FC Road, Pune',
            'course_ids' => [$mpsc->id, $commerce->id],
            'otp' => '654321',
        ]);

        $verifyRes->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'mobile_number', 'selected_courses'],
                ],
            ]);

        $user = User::where('mobile_number', '9123456780')->first();
        $this->assertNotNull($user);
        $this->assertCount(2, $user->courses);
    }
}
