<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiExamTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Course $course;

    protected Exam $exam;

    protected Question $question;

    protected QuestionOption $optA;

    protected QuestionOption $optB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['mobile_number' => '9876543210']);

        $this->course = Course::create([
            'name' => 'Police Bharti',
            'is_active' => true,
        ]);

        $this->user->courses()->attach($this->course->id);

        $this->exam = Exam::create([
            'course_id' => $this->course->id,
            'title' => 'Police Bharti Written Test Mock 1',
            'slug' => 'police-bharti-mock-1',
            'duration_minutes' => 30,
            'total_marks' => 2.0,
            'pass_percentage' => 50.0,
            'has_negative_marking' => true,
            'default_negative_marks' => 0.25,
            'is_published' => true,
            'is_premium' => false,
        ]);

        $category = Category::create([
            'course_id' => $this->course->id,
            'name' => 'General Knowledge',
            'is_active' => true,
        ]);

        $this->question = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $category->id,
            'question_text' => 'Where is the Gateway of India located?',
            'explanation_text' => 'It is located in Mumbai.',
            'status' => 'PUBLISHED',
        ]);

        $this->optA = QuestionOption::create([
            'question_id' => $this->question->id,
            'option_key' => 'A',
            'option_text' => 'Mumbai',
            'is_correct' => true,
        ]);

        $this->optB = QuestionOption::create([
            'question_id' => $this->question->id,
            'option_key' => 'B',
            'option_text' => 'Delhi',
            'is_correct' => false,
        ]);

        ExamQuestion::create([
            'exam_id' => $this->exam->id,
            'question_id' => $this->question->id,
            'marks' => 2.0,
            'negative_marks' => 0.5,
            'sort_order' => 1,
        ]);
    }

    public function test_start_exam_creates_attempt_with_authoritative_timer(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/v1/exams/{$this->exam->id}/start");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'exam_id' => $this->exam->id,
                    'duration_minutes' => 30,
                ],
            ]);

        $this->assertGreaterThan(1700, $response->json('data.remaining_seconds'));
    }

    public function test_complete_exam_flow(): void
    {
        Sanctum::actingAs($this->user);

        // 1. Start exam
        $startRes = $this->postJson("/api/v1/exams/{$this->exam->id}/start");
        $attemptId = $startRes->json('data.attempt_id');

        // 2. Answer question
        $ansRes = $this->postJson("/api/v1/exams/attempts/{$attemptId}/answer", [
            'question_id' => $this->question->id,
            'selected_option_id' => $this->optA->id,
        ]);
        $ansRes->assertStatus(200);

        // 3. Submit exam
        $subRes = $this->postJson("/api/v1/exams/attempts/{$attemptId}/submit");
        $subRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_passed' => true,
                    'score_obtained' => 2.0,
                ],
            ]);

        // 4. View scorecard result
        $resultRes = $this->getJson("/api/v1/exams/attempts/{$attemptId}/result");
        $resultRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'score_obtained' => 2.0,
                    'total_marks' => 2.0,
                    'percentage' => 100.0,
                    'is_passed' => true,
                    'correct_answers' => 1,
                ],
            ]);
    }
}
