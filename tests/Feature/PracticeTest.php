<?php

namespace Tests\Feature;

use App\Livewire\Student\Dashboard;
use App\Livewire\Student\Practice\PracticeEngine;
use App\Models\Category;
use App\Models\Course;
use App\Models\PracticeAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PracticeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Course $course;

    protected Category $category;

    protected Question $question;

    protected QuestionOption $correctOption;

    protected QuestionOption $wrongOption;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'mobile_number' => '9876543210',
        ]);

        $this->course = Course::create([
            'name' => 'General Science Course',
            'is_active' => true,
            'free_question_limit' => 2,
        ]);

        $this->user->courses()->attach($this->course->id);

        $this->category = Category::create([
            'course_id' => $this->course->id,
            'name' => 'Physics',
            'is_active' => true,
        ]);

        $this->question = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $this->category->id,
            'question_text' => 'What is the speed of light in vacuum?',
            'question_html' => '<p>What is the speed of light in vacuum?</p>',
            'explanation_text' => 'It is approx 300,000 km/s.',
            'difficulty' => 'Easy',
            'status' => 'PUBLISHED',
            'is_free' => true,
        ]);

        $this->correctOption = QuestionOption::create([
            'question_id' => $this->question->id,
            'option_key' => 'A',
            'option_text' => '3 x 10^8 m/s',
            'is_correct' => true,
            'sort_order' => 0,
        ]);

        $this->wrongOption = QuestionOption::create([
            'question_id' => $this->question->id,
            'option_key' => 'B',
            'option_text' => '3 x 10^6 m/s',
            'is_correct' => false,
            'sort_order' => 1,
        ]);
    }

    public function test_student_dashboard_renders_successfully_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Student Dashboard')
            ->assertSee('General Science Course')
            ->assertSee('Manage Target Courses');
    }

    public function test_student_can_toggle_and_save_target_courses_on_dashboard(): void
    {
        $newCourse = Course::create([
            'name' => 'Advanced Math Course',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSet('showCourseManager', false)
            ->call('toggleCourseManager')
            ->assertSet('showCourseManager', true)
            ->assertSee('Choose Your Target Courses')
            ->set('selectedCourseIds', [$this->course->id, $newCourse->id])
            ->call('saveCourses')
            ->assertSet('showCourseManager', false);

        $this->assertTrue($this->user->courses()->where('courses.id', $newCourse->id)->exists());
    }

    public function test_student_can_answer_question_correctly_in_practice_engine(): void
    {
        Livewire::actingAs($this->user)
            ->test(PracticeEngine::class, ['course' => $this->course->id])
            ->assertSet('course_id', $this->course->id)
            ->assertSee('What is the speed of light in vacuum?')
            ->call('selectOption', $this->correctOption->id)
            ->assertSet('hasSubmitted', true)
            ->assertSet('isCurrentCorrect', true);

        $this->assertDatabaseHas('practice_attempts', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'course_id' => $this->course->id,
            'selected_option_id' => $this->correctOption->id,
            'is_correct' => true,
            'attempt_count' => 1,
        ]);

        $this->assertEquals(1, $this->user->uniqueQuestionsAttemptedCount($this->course->id));
        $this->assertEquals(1, $this->user->correctAttemptsCount($this->course->id));
        $this->assertEquals(100.0, $this->user->accuracyPercentage($this->course->id));
    }

    public function test_repeated_practice_attempts_do_not_inflate_unique_question_count(): void
    {
        // First attempt: wrong option
        Livewire::actingAs($this->user)
            ->test(PracticeEngine::class, ['course' => $this->course->id])
            ->call('selectOption', $this->wrongOption->id);

        $this->assertEquals(1, $this->user->uniqueQuestionsAttemptedCount($this->course->id));

        // Re-attempt the same question: correct option
        Livewire::actingAs($this->user)
            ->test(PracticeEngine::class, ['course' => $this->course->id])
            ->set('hasSubmitted', false)
            ->call('selectOption', $this->correctOption->id);

        // Unique question count should still be 1, but attempt_count is 2
        $this->assertEquals(1, $this->user->uniqueQuestionsAttemptedCount($this->course->id));

        $this->assertDatabaseHas('practice_attempts', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'attempt_count' => 2,
            'is_correct' => true,
        ]);
    }

    public function test_free_quota_limit_is_strictly_enforced_for_unsubscribed_student(): void
    {
        // Limit is 2 questions. Create two more questions
        $q2 = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $this->category->id,
            'question_text' => 'Question 2 text',
            'status' => 'PUBLISHED',
        ]);
        $opt2 = QuestionOption::create([
            'question_id' => $q2->id,
            'option_key' => 'A',
            'option_text' => 'Option A',
            'is_correct' => true,
        ]);

        $q3 = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $this->category->id,
            'question_text' => 'Question 3 text',
            'status' => 'PUBLISHED',
        ]);
        $opt3 = QuestionOption::create([
            'question_id' => $q3->id,
            'option_key' => 'A',
            'option_text' => 'Option A',
            'is_correct' => true,
        ]);

        // Attempt Question 1
        PracticeAttempt::create([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'question_id' => $this->question->id,
            'is_correct' => true,
        ]);

        // Attempt Question 2
        PracticeAttempt::create([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'question_id' => $q2->id,
            'is_correct' => true,
        ]);

        // Free limit (2) is now exhausted
        $this->assertTrue($this->user->hasExceededFreeQuota($this->course->id));
        $this->assertEquals(0, $this->user->freeQuotaRemaining($this->course->id));

        // Attempting a 3rd new question in PracticeEngine should trigger quotaExceededAlert
        Livewire::actingAs($this->user)
            ->test(PracticeEngine::class, ['course' => $this->course->id])
            ->set('currentIndex', 2) // Q3
            ->call('selectOption', $opt3->id)
            ->assertSet('quotaExceededAlert', true)
            ->assertSet('hasSubmitted', false);

        // Q3 should NOT be recorded in practice_attempts
        $this->assertDatabaseMissing('practice_attempts', [
            'user_id' => $this->user->id,
            'question_id' => $q3->id,
        ]);
    }

    public function test_student_can_toggle_bookmarks(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(PracticeEngine::class, ['course' => $this->course->id])
            ->assertSet('isBookmarked', false)
            ->call('toggleBookmark')
            ->assertSet('isBookmarked', true);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
        ]);

        $component->call('toggleBookmark')
            ->assertSet('isBookmarked', false);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
        ]);
    }
}
