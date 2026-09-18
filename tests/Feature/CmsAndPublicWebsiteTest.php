<?php

namespace Tests\Feature;

use App\Livewire\Admin\Content\AnnouncementManager;
use App\Livewire\Admin\Content\NewsManager;
use App\Livewire\Admin\Content\SliderManager;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\NewsArticle;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\CmsPermissionSeeder;
use Database\Seeders\ParikshaMitraContentSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CmsAndPublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            SettingSeeder::class,
            CmsPermissionSeeder::class,
            ParikshaMitraContentSeeder::class,
        ]);

        $this->student = User::factory()->create([
            'name' => 'Test Student',
            'mobile_number' => '9111122222',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'mobile_number' => '9888899999',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
        $this->admin->roles()->attach($adminRole->id);
    }

    public function test_public_homepage_loads_with_brand_and_streams(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('RGPL');
        $response->assertSee('Academy');
        $response->assertSee('Your Exam Preparation Partner');
        $response->assertSee('MPSC');
        $response->assertSee('Police Bharti');
        $response->assertSee('Commerce');
        $response->assertSee('Practice Your Way');
        $response->assertSee('RGPL Academy at a Glance');
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('livewire.js', false);
        $response->assertSee('[x-cloak]', false);
        $response->assertSee('Previous Slide');
        $response->assertSee('Next Slide');
    }

    public function test_rule_50_guest_cannot_access_protected_student_engine(): void
    {
        // Guests trying to access student practice or exams must be redirected to login
        $this->get('/student/practice')->assertRedirect('/login');
        $this->get('/student/exams')->assertRedirect('/login');

        // Explainer pages load for guests without exposing question bank items or answers
        $explainerPractice = $this->get('/practice');
        $explainerPractice->assertStatus(200);
        $explainerPractice->assertSee('Practice Mode');
        $explainerPractice->assertDontSee('correct_answer');

        $explainerExams = $this->get('/exams');
        $explainerExams->assertStatus(200);
        $explainerExams->assertSee('Timed Mock Examination Mode');
    }

    public function test_courses_listing_and_commerce_subcategories(): void
    {
        $response = $this->get('/courses');
        $response->assertStatus(200);
        $response->assertSee('MPSC');
        $response->assertSee('Police Bharti');
        $response->assertSee('Commerce');

        // Commerce course page must show 11th, 12th, CA, CS, ICWA categories
        $commerceResponse = $this->get('/courses/commerce');
        $commerceResponse->assertStatus(200);
        $commerceResponse->assertSee('11th Commerce');
        $commerceResponse->assertSee('12th Commerce');
        $commerceResponse->assertSee('CA (Chartered Accountancy)');
        $commerceResponse->assertSee('CS (Company Secretary)');
        $commerceResponse->assertSee('ICWA / CMA');
    }

    public function test_news_articles_and_slug_routing(): void
    {
        $response = $this->get('/announcements-and-news');
        $response->assertStatus(200);

        // Published article
        $article = NewsArticle::where('status', NewsArticle::STATUS_PUBLISHED)->first();
        $this->assertNotNull($article);

        $showResponse = $this->get('/news/'.$article->slug);
        $showResponse->assertStatus(200);
        $showResponse->assertSee($article->title);

        // Draft article should 404 for public guests
        $draftArticle = NewsArticle::create([
            'title' => 'Secret Draft Syllabus',
            'slug' => 'secret-draft-syllabus',
            'content_html' => '<p>Secret</p>',
            'status' => NewsArticle::STATUS_DRAFT,
        ]);

        $this->get('/news/'.$draftArticle->slug)->assertStatus(404);
    }

    public function test_contact_form_submission_and_honeypot_trap(): void
    {
        $response = $this->get('/contact-us');
        $response->assertStatus(200);

        // Legitimate submission
        $postResponse = $this->post('/contact-us', [
            'name' => 'Aspirant Ramesh',
            'mobile' => '9822012345',
            'email' => 'ramesh@example.com',
            'subject' => 'Inquiry about Police Bharti Mock Tests',
            'message' => 'Are previous year question papers included in the Police Bharti pass?',
            'website_url_check' => '', // Clean honeypot
        ]);

        $postResponse->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Aspirant Ramesh',
            'mobile' => '9822012345',
            'status' => 'NEW',
        ]);

        // Honeypot spam submission
        $spamResponse = $this->post('/contact-us', [
            'name' => 'Spam Bot',
            'mobile' => '9999999999',
            'email' => 'bot@spammer.com',
            'subject' => 'Buy cheap backlinks',
            'message' => 'Spam message',
            'website_hp' => 'http://spam-link.com', // Filled honeypot
        ]);

        $spamResponse->assertSessionHas('success'); // Pretends success to confuse bot
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'bot@spammer.com',
            'status' => 'SPAM',
        ]);
        $this->assertDatabaseMissing('contact_messages', [
            'email' => 'bot@spammer.com',
            'status' => 'NEW',
        ]);
    }

    public function test_legal_static_pages_load_successfully(): void
    {
        $this->get('/about-us')->assertStatus(200)->assertSee('About RGPL Academy');
        $this->get('/privacy-policy')->assertStatus(200)->assertSee('Privacy Policy');
        $this->get('/terms-and-conditions')->assertStatus(200)->assertSee('Terms and Conditions');
        $this->get('/refund-policy')->assertStatus(200)->assertSee('Refund Policy');
    }

    public function test_pricing_page_loads_and_handles_settings_cache_cleanly(): void
    {
        $response = $this->get('/pricing');
        $response->assertStatus(200);
        $response->assertSee('Subscription Plans');
        $response->assertSee('RGPL Academy');

        // Test second request hitting cache
        $secondResponse = $this->get('/pricing');
        $secondResponse->assertStatus(200);
        $secondResponse->assertSee('Subscription Plans');
    }

    public function test_search_functionality(): void
    {
        $response = $this->get('/search?q=Commerce');
        $response->assertStatus(200);
        $response->assertSee('Commerce');
    }

    public function test_admin_cms_routes_protected_by_super_admin_role(): void
    {
        // Unauthenticated visitor
        $this->get('/admin/sliders')->assertRedirect('/login');

        // Normal student
        $this->actingAs($this->student)
            ->get('/admin/sliders')
            ->assertStatus(403);

        // Super Admin
        $this->actingAs($this->admin)
            ->get('/admin/sliders')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/homepage-sections')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/announcements')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/news')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/faqs')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/testimonials')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/pages')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/contact-messages')
            ->assertStatus(200);
    }

    public function test_admin_can_manage_sliders_via_livewire(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SliderManager::class)
            ->set('title', 'Special MPSC 2026 Batch')
            ->set('subtitle', 'Batch Commencing Soon')
            ->set('button_text', 'Join Now')
            ->set('button_url', '/courses/mpsc')
            ->set('is_active', true)
            ->set('display_order', 10)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('homepage_sliders', [
            'title' => 'Special MPSC 2026 Batch',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_manage_announcements_workflow(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AnnouncementManager::class)
            ->set('title', 'Police Bharti Physical Exam Hall Ticket')
            ->set('short_description', 'Download available from official portal')
            ->set('priority', 'HIGH')
            ->set('status', Announcement::STATUS_PUBLISHED)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('announcements', [
            'title' => 'Police Bharti Physical Exam Hall Ticket',
            'priority' => 'HIGH',
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_admin_can_soft_delete_and_restore_news(): void
    {
        $article = NewsArticle::create([
            'title' => 'Temporary News Item',
            'slug' => 'temporary-news-item',
            'content_html' => '<p>Details</p>',
            'status' => NewsArticle::STATUS_PUBLISHED,
        ]);

        Livewire::actingAs($this->admin)
            ->test(NewsManager::class)
            ->call('delete', $article->id);

        $this->assertSoftDeleted('news_articles', ['id' => $article->id]);

        Livewire::actingAs($this->admin)
            ->test(NewsManager::class)
            ->set('showTrashed', true)
            ->call('restore', $article->id);

        $this->assertNotSoftDeleted('news_articles', ['id' => $article->id]);
    }

    public function test_logout_from_super_admin_renders_homepage_cleanly(): void
    {
        $this->actingAs($this->admin);

        // Verify admin access
        $this->get('/admin/sliders')->assertStatus(200);

        // Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();

        // Follow redirect to homepage
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('RGPL');
        $homeResponse->assertSee('Academy');
        $homeResponse->assertSee('MPSC');
    }
}
