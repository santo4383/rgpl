<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPublicContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_returns_aggregated_data(): void
    {
        Announcement::create([
            'title' => 'Important Exam Notification 2026',
            'short_description' => 'Registration is now open.',
            'content' => 'Full announcement text.',
            'category' => 'Exam',
            'status' => 'PUBLISHED',
            'featured' => true,
            'priority' => 'HIGH',
        ]);

        Course::create([
            'name' => 'Maharashtra Police Bharti',
            'slug' => 'police-bharti',
            'description' => 'Complete prep guide',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/public/home');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'top_announcement',
                    'sliders',
                    'courses',
                    'statistics',
                    'featured_categories',
                    'latest_news',
                    'latest_announcements',
                    'testimonials',
                    'plans',
                    'faqs',
                    'free_limit',
                ],
            ]);

        $this->assertEquals('Important Exam Notification 2026', $response->json('data.top_announcement.title'));
    }

    public function test_public_search_returns_results(): void
    {
        Course::create([
            'name' => 'MPSC State Services',
            'slug' => 'mpsc-state-services',
            'description' => 'Civil services test series',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/public/search?q=MPSC');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'query',
                    'courses',
                    'categories',
                    'announcements',
                    'news',
                    'total_results',
                ],
            ]);

        $this->assertGreaterThanOrEqual(1, $response->json('data.total_results'));
    }

    public function test_public_contact_submission_stores_message(): void
    {
        $payload = [
            'name' => 'Rohan Sharma',
            'mobile' => '9876543210',
            'email' => 'rohan@example.com',
            'subject' => 'Inquiry regarding mock tests',
            'message' => 'Please provide syllabus breakdown for Talathi exam.',
        ];

        $response = $this->postJson('/api/v1/public/contact', $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Rohan Sharma',
            'mobile' => '9876543210',
            'status' => ContactMessage::STATUS_NEW,
        ]);
    }

    public function test_public_contact_honeypot_triggers_spam_status(): void
    {
        $payload = [
            'name' => 'Spam Bot',
            'mobile' => '1234567890',
            'subject' => 'Buy our pills',
            'message' => 'Spam links here',
            'website_hp' => 'http://spamurl.com',
        ];

        $response = $this->postJson('/api/v1/public/contact', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Spam Bot',
            'status' => ContactMessage::STATUS_SPAM,
        ]);
    }

    public function test_public_about_returns_page_info(): void
    {
        Page::create([
            'title' => 'About RGPL Academy',
            'slug' => 'about-us',
            'content_html' => '<p>Leading competitive exam platform.</p>',
            'status' => 'PUBLISHED',
        ]);

        $response = $this->getJson('/api/v1/public/about');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'title',
                    'subtitle',
                    'content_html',
                    'features',
                ],
            ]);
    }

    public function test_guests_can_view_exam_list(): void
    {
        $course = Course::create([
            'name' => 'Talathi Bharti',
            'slug' => 'talathi-bharti',
            'is_active' => true,
        ]);

        Exam::create([
            'course_id' => $course->id,
            'title' => 'Grand Mock Test 1',
            'slug' => 'grand-mock-test-1',
            'duration_minutes' => 60,
            'total_marks' => 100,
            'passing_marks' => 40,
            'status' => 'PUBLISHED',
        ]);

        $response = $this->getJson('/api/v1/exams');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }
}
