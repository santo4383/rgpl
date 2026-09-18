<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OtpAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Enter your mobile number');
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Register for a new student account');
    }

    public function test_user_can_request_login_otp_and_authenticate(): void
    {
        $user = User::factory()->create([
            'mobile_number' => '9123456780',
        ]);

        // Request OTP
        $response = $this->post(route('login.send-otp'), [
            'mobile_number' => '9123456780',
        ]);
        $response->assertSessionHas('otp_login_mobile', '9123456780');

        // Set the OTP hash for the generated record
        DB::table('otp_verifications')
            ->where('mobile_number', '9123456780')
            ->update(['otp_hash' => Hash::make('654321')]);

        $verifyResponse = $this->post(route('login.verify'), [
            'mobile_number' => '9123456780',
            'otp' => '654321',
        ]);

        $this->assertAuthenticatedAs($user);
        $verifyResponse->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_login_page_renders_otp_step_when_requested_and_allows_reset(): void
    {
        $user = User::factory()->create([
            'mobile_number' => '9876543210',
        ]);

        // Request OTP
        $this->post(route('login.send-otp'), ['mobile_number' => '9876543210']);

        // Next visit to login shows OTP step and the registered number
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Verify One-Time Password');
        $response->assertSee('+91 9876543210');
        $response->assertSee('Change Mobile Number');

        // Clicking reset resets back to mobile number input
        $resetResponse = $this->get(route('login', ['reset' => 1]));
        $resetResponse->assertRedirect(route('login'));

        $freshResponse = $this->get('/login');
        $freshResponse->assertStatus(200);
        $freshResponse->assertSee('Student &amp; Aspirant Login', false);
        $freshResponse->assertSee('Enter your mobile number');
    }

    public function test_login_page_auto_resets_stale_legacy_sessions(): void
    {
        // Simulate a legacy session that had mobile number but no timestamp
        $response = $this->withSession(['otp_login_mobile' => '9999988888'])
            ->get('/login');

        $response->assertStatus(200);
        $response->assertSessionMissing('otp_login_mobile');
        $response->assertSee('Student &amp; Aspirant Login', false);
        $response->assertSee('Enter your mobile number');
    }
}
