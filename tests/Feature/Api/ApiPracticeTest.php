<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiPracticeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Course $course;

    protected Question $question;

    protected QuestionOption $correctOption;

    protected QuestionOption $wrongOption;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['mobile_number' => '9876543210']);

        $this->course = Course::create([
            'name' => 'MPSC State Services',
            'is_active' => true,
            'free_question_limit' => 1, // small quota for easy testing
        ]);

        $this->user->courses()->attach($this->course->id);

        $category = Category::create([
            'course_id' => $this->course->id,
            'name' => 'General Studies',
            'is_active' => true,
        ]);

        $this->question = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $category->id,
            'question_text' => 'What is the highest court in India?',
            'question_html' => '<p>What is the highest court in India?</p>',
            'explanation_text' => 'The Supreme Court of India is the highest judicial court.',
            'difficulty' => 'Easy',
            'status' => 'PUBLISHED',
        ]);

        $this->correctOption = QuestionOption::create([
            'question_id' => $this->question->id,
            'option_key' => 'A',
            'option_text' => 'Supreme Court of India',
            'option_html' => '<p>Supreme Court of India</p>',
            'is_correct' => true,
        ]);

        $this->wrongOption = QuestionOption::create([
            'question_id' => $this->question->id,
            'option_key' => 'B',
            'option_text' => 'High Court',
            'option_html' => '<p>High Court</p>',
            'is_correct' => false,
        ]);
    }

    public function test_questions_endpoint_returns_decrypted_text_without_exposing_correct_answers(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/practice/questions?course_id={$this->course->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $qData = $response->json('data.0');
        $this->assertEquals('What is the highest court in India?', $qData['question_text']);
        $this->assertNull($qData['explanation_text']); // not revealed before answer

        // Verify options do NOT reveal is_correct
        foreach ($qData['options'] as $opt) {
            $this->assertNull($opt['is_correct'], 'Correct answer must NOT be exposed before submission');
        }
    }

    public function test_submit_answer_returns_evaluation_and_explanation(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/practice/submit', [
            'question_id' => $this->question->id,
            'selected_option_id' => $this->correctOption->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_correct' => true,
                    'correct_option_id' => $this->correctOption->id,
                    'explanation_text' => 'The Supreme Court of India is the highest judicial court.',
                ],
            ]);
    }

    public function test_free_quota_exceeded_returns_free_limit_reached(): void
    {
        Sanctum::actingAs($this->user);

        // 1st attempt reaches limit of 1
        $this->postJson('/api/v1/practice/submit', [
            'question_id' => $this->question->id,
            'selected_option_id' => $this->correctOption->id,
        ])->assertStatus(200);

        // Create a 2nd question
        $q2 = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $this->question->category_id,
            'question_text' => 'Second question',
            'status' => 'PUBLISHED',
        ]);
        $opt2 = QuestionOption::create([
            'question_id' => $q2->id,
            'option_key' => 'A',
            'option_text' => 'Option 1',
            'is_correct' => true,
        ]);

        // Attempting 2nd question without subscription should fail with FREE_LIMIT_REACHED
        $res = $this->postJson('/api/v1/practice/submit', [
            'question_id' => $q2->id,
            'selected_option_id' => $opt2->id,
        ]);

        $res->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'FREE_LIMIT_REACHED',
                ],
            ]);
    }
}
