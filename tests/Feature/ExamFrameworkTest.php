<?php

namespace Tests\Feature;

use App\Livewire\Admin\Exams\ExamForm;
use App\Livewire\Admin\Exams\ExamQuestions;
use App\Livewire\Student\Exams\ExamList;
use App\Livewire\Student\Exams\ExamTake;
use App\Models\Category;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExamFrameworkTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $admin;

    protected Course $course;

    protected Question $question1;

    protected Question $question2;

    protected QuestionOption $q1OptA;

    protected QuestionOption $q1OptB;

    protected QuestionOption $q2OptA;

    protected QuestionOption $q2OptB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'mobile_number' => '9877766666',
        ]);

        $this->admin = User::factory()->create([
            'mobile_number' => '9988877777',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
        $this->admin->roles()->attach($adminRole->id);

        $this->course = Course::create([
            'name' => 'MPSC Examination Course',
            'is_active' => true,
            'free_question_limit' => 500,
        ]);

        $category = Category::create([
            'course_id' => $this->course->id,
            'name' => 'General Studies',
            'is_active' => true,
        ]);

        $this->student->courses()->attach($this->course->id);

        // Create 2 test questions
        $this->question1 = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $category->id,
            'question_type' => 'STANDARD_MCQ',
            'language' => 'en',
            'question_text' => 'What is the capital of Maharashtra?',
            'difficulty' => 'Easy',
            'status' => 'PUBLISHED',
            'created_by' => $this->admin->id,
        ]);

        $this->q1OptA = QuestionOption::create([
            'question_id' => $this->question1->id,
            'option_key' => 'A',
            'option_text' => 'Mumbai',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $this->q1OptB = QuestionOption::create([
            'question_id' => $this->question1->id,
            'option_key' => 'B',
            'option_text' => 'Pune',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        $this->question2 = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $category->id,
            'question_type' => 'STANDARD_MCQ',
            'language' => 'en',
            'question_text' => 'Which river flows through Pune?',
            'difficulty' => 'Medium',
            'status' => 'PUBLISHED',
            'created_by' => $this->admin->id,
        ]);

        $this->q2OptA = QuestionOption::create([
            'question_id' => $this->question2->id,
            'option_key' => 'A',
            'option_text' => 'Mula-Mutha',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $this->q2OptB = QuestionOption::create([
            'question_id' => $this->question2->id,
            'option_key' => 'B',
            'option_text' => 'Godavari',
            'is_correct' => false,
            'sort_order' => 2,
        ]);
    }

    public function test_admin_can_view_exams_list()
    {
        $this->actingAs($this->admin);

        Exam::create([
            'course_id' => $this->course->id,
            'title' => 'MPSC Prelims Mock 1',
            'duration_minutes' => 60,
            'total_marks' => 100.0,
            'pass_percentage' => 40.0,
            'is_published' => true,
        ]);

        $response = $this->get(route('admin.exams.index'));
        $response->assertStatus(200);
        $response->assertSee('MPSC Prelims Mock 1');
    }

    public function test_admin_can_create_exam_with_negative_marking_config()
    {
        $this->actingAs($this->admin);

        Livewire::test(ExamForm::class)
            ->set('course_id', $this->course->id)
            ->set('title', 'Police Special Mock Test')
            ->set('duration_minutes', 45)
            ->set('total_marks', 50.0)
            ->set('pass_percentage', 40.0)
            ->set('has_negative_marking', true)
            ->set('default_negative_marks', 0.25)
            ->set('is_published', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('exams', [
            'title' => 'Police Special Mock Test',
            'has_negative_marking' => true,
            'default_negative_marks' => 0.25,
            'total_marks' => 50.0,
        ]);
    }

    public function test_admin_cannot_assign_questions_exceeding_total_marks()
    {
        $this->actingAs($this->admin);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'title' => 'Mini Exam',
            'duration_minutes' => 15,
            'total_marks' => 2.0, // Only 2 marks allowed
            'pass_percentage' => 40.0,
            'has_negative_marking' => true,
            'default_negative_marks' => 0.25,
            'is_published' => true,
        ]);

        // Question 1 assigned with 2.0 marks (100% capacity)
        Livewire::test(ExamQuestions::class, ['exam' => $exam])
            ->set("addMarks.{$this->question1->id}", 2.0)
            ->call('addQuestion', $this->question1->id)
            ->assertHasNoErrors();

        $this->assertEquals(2.0, $exam->assignedMarksTotal());

        // Attempting to add Question 2 with 1.0 marks should fail because 2 + 1 > 2.0
        Livewire::test(ExamQuestions::class, ['exam' => $exam])
            ->set("addMarks.{$this->question2->id}", 1.0)
            ->call('addQuestion', $this->question2->id)
            ->assertHasErrors(['allocation']);

        // Assert question2 was NOT assigned
        $this->assertEquals(1, $exam->examQuestions()->count());
    }

    public function test_student_can_view_published_exams()
    {
        $this->actingAs($this->student);

        Exam::create([
            'course_id' => $this->course->id,
            'title' => 'Maharashtra General Mock Test',
            'duration_minutes' => 60,
            'total_marks' => 10.0,
            'pass_percentage' => 40.0,
            'is_published' => true,
        ]);

        $response = $this->get(route('student.exams'));
        $response->assertStatus(200);
        $response->assertSee('Maharashtra General Mock Test');
    }

    public function test_non_subscribed_student_cannot_start_premium_exam()
    {
        $this->actingAs($this->student);

        $premiumExam = Exam::create([
            'course_id' => $this->course->id,
            'title' => 'VIP Mock Test',
            'duration_minutes' => 60,
            'total_marks' => 10.0,
            'is_published' => true,
            'is_premium' => true,
        ]);

        Livewire::test(ExamList::class)
            ->set('selectedExam', $premiumExam)
            ->call('startExam')
            ->assertRedirect(route('student.plans'));

        $this->assertDatabaseMissing('exam_attempts', [
            'user_id' => $this->student->id,
            'exam_id' => $premiumExam->id,
        ]);
    }

    public function test_student_can_start_exam_and_exam_answers_are_created()
    {
        $this->actingAs($this->student);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'title' => 'Open Mock Test',
            'duration_minutes' => 30,
            'total_marks' => 2.0,
            'pass_percentage' => 50.0,
            'has_negative_marking' => true,
            'default_negative_marks' => 0.25,
            'is_published' => true,
            'is_premium' => false,
        ]);

        $exam->examQuestions()->create(['question_id' => $this->question1->id, 'marks' => 1.0, 'negative_marks' => 0.25, 'sort_order' => 1]);
        $exam->examQuestions()->create(['question_id' => $this->question2->id, 'marks' => 1.0, 'negative_marks' => 0.25, 'sort_order' => 2]);

        Livewire::test(ExamList::class)
            ->set('selectedExam', $exam)
            ->call('startExam');

        $attempt = ExamAttempt::where('user_id', $this->student->id)->where('exam_id', $exam->id)->first();
        $this->assertNotNull($attempt);
        $this->assertEquals('IN_PROGRESS', $attempt->status);
        $this->assertEquals(2, $attempt->answers()->count());
    }

    public function test_exam_submission_grades_correct_and_incorrect_with_negative_marking()
    {
        $this->actingAs($this->student);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'title' => 'Scoring Test',
            'duration_minutes' => 30,
            'total_marks' => 2.0,
            'pass_percentage' => 50.0,
            'has_negative_marking' => true,
            'default_negative_marks' => 0.25,
            'is_published' => true,
            'is_premium' => false,
        ]);

        $exam->examQuestions()->create(['question_id' => $this->question1->id, 'marks' => 1.0, 'negative_marks' => 0.25, 'sort_order' => 1]);
        $exam->examQuestions()->create(['question_id' => $this->question2->id, 'marks' => 1.0, 'negative_marks' => 0.25, 'sort_order' => 2]);

        $attempt = ExamAttempt::create([
            'user_id' => $this->student->id,
            'exam_id' => $exam->id,
            'started_at' => now(),
            'status' => 'IN_PROGRESS',
            'total_questions' => 2,
            'total_marks' => 2.0,
        ]);

        $attempt->answers()->create(['question_id' => $this->question1->id]);
        $attempt->answers()->create(['question_id' => $this->question2->id]);

        // Student selects Q1 correctly (Option A) and Q2 incorrectly (Option B)
        Livewire::test(ExamTake::class, ['attempt' => $attempt])
            ->set('currentIndex', 0)
            ->call('selectOption', $this->q1OptA->id) // Correct (+1.0)
            ->call('jumpToQuestion', 1)
            ->call('selectOption', $this->q2OptB->id) // Incorrect (-0.25)
            ->call('submit', false)
            ->assertRedirect(route('student.exams.result', $attempt->id));

        $attempt->refresh();
        $this->assertEquals('SUBMITTED', $attempt->status);
        $this->assertEquals(1, $attempt->correct_answers);
        $this->assertEquals(1, $attempt->incorrect_answers);
        $this->assertIsInt($attempt->time_spent_seconds);
        // Score: 1.0 - 0.25 = 0.75
        $this->assertEquals(0.75, $attempt->score_obtained);
        // Percentage: 0.75 / 2.0 = 37.5%
        $this->assertEquals(37.5, $attempt->percentage);
        $this->assertFalse($attempt->is_passed); // Needed 50%
    }

    public function test_student_navigates_to_exams_with_course_query_param_auto_selects_course()
    {
        $this->actingAs($this->student);

        // Call ExamList with course query parameter
        Livewire::withQueryParams(['course' => $this->course->id])
            ->test(ExamList::class)
            ->assertSet('course_id', (string) $this->course->id);
    }

    public function test_student_cannot_retake_exam_once_submitted_single_attempt_policy()
    {
        $this->actingAs($this->student);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'title' => 'Single Attempt Only Exam',
            'duration_minutes' => 30,
            'total_marks' => 10.0,
            'pass_percentage' => 40.0,
            'is_published' => true,
        ]);

        $attempt = ExamAttempt::create([
            'user_id' => $this->student->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
            'status' => 'SUBMITTED',
            'total_questions' => 1,
            'score_obtained' => 10.0,
            'total_marks' => 10.0,
            'percentage' => 100.0,
            'is_passed' => true,
        ]);

        // Student clicks startExam again
        Livewire::test(ExamList::class)
            ->set('selectedExam', $exam)
            ->call('startExam')
            ->assertRedirect(route('student.exams.result', $attempt->id));

        // Ensure no new attempt was created
        $this->assertEquals(1, ExamAttempt::where('user_id', $this->student->id)->where('exam_id', $exam->id)->count());

        // Visiting exam take directly redirects to result
        Livewire::test(ExamTake::class, ['attempt' => $attempt])
            ->assertRedirect(route('student.exams.result', $attempt->id));
    }
}
