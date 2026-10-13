<?php

namespace Tests\Feature\Auth;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'STUDENT'], ['description' => 'Student']);
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Student Registration');
        $response->assertSee('Password');
        $response->assertSee('Confirm Password');
    }

    public function test_new_students_can_register_with_password()
    {
        $course = Course::create(['name' => 'UPSC Preliminary', 'slug' => 'upsc-preliminary']);

        $response = $this->post('/register', [
            'name' => 'Rahul Sharma',
            'mobile_number' => '9876543210',
            'dob' => '2001-05-15',
            'address' => 'Pune, Maharashtra',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'course_ids' => [$course->id],
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::where('mobile_number', '9876543210')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Rahul Sharma', $user->name);
        $this->assertTrue(Hash::check('secret1234', $user->password));
        $this->assertTrue($user->hasRole('STUDENT'));
        $this->assertTrue($user->courses->contains($course));
    }

    public function test_registration_fails_if_password_confirmation_does_not_match()
    {
        $response = $this->post('/register', [
            'name' => 'Rahul Sharma',
            'mobile_number' => '9876543210',
            'dob' => '2001-05-15',
            'address' => 'Pune, Maharashtra',
            'password' => 'secret1234',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_fails_if_password_is_too_short()
    {
        $response = $this->post('/register', [
            'name' => 'Rahul Sharma',
            'mobile_number' => '9876543210',
            'dob' => '2001-05-15',
            'address' => 'Pune, Maharashtra',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Student & Aspirant Login');
        $response->assertSee('Mobile Number');
        $response->assertSee('Password');
    }

    public function test_students_can_authenticate_using_mobile_and_password()
    {
        $user = User::create([
            'name' => 'Anil Kumar',
            'mobile_number' => '9123456789',
            'dob' => '1999-10-10',
            'address' => 'Mumbai, Maharashtra',
            'password' => Hash::make('mypassword123'),
        ]);

        $response = $this->post('/login', [
            'mobile_number' => '9123456789',
            'password' => 'mypassword123',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_password()
    {
        User::create([
            'name' => 'Anil Kumar',
            'mobile_number' => '9123456789',
            'dob' => '1999-10-10',
            'address' => 'Mumbai, Maharashtra',
            'password' => Hash::make('mypassword123'),
        ]);

        $response = $this->post('/login', [
            'mobile_number' => '9123456789',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['mobile_number']);
        $this->assertGuest();
    }

    public function test_login_fails_with_non_existent_mobile()
    {
        $response = $this->post('/login', [
            'mobile_number' => '9000000000',
            'password' => 'anypassword',
        ]);

        $response->assertSessionHasErrors(['mobile_number']);
        $this->assertGuest();
    }
}
