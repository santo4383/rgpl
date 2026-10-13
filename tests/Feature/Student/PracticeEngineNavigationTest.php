<?php

namespace Tests\Feature\Student;

use App\Livewire\Student\Practice\PracticeEngine;
use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PracticeEngineNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected Course $course;

    protected Question $q1;

    protected Question $q2;

    protected QuestionOption $q1OptA;

    protected QuestionOption $q1OptB;

    protected QuestionOption $q2OptA;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'STUDENT', 'display_name' => 'Student']);
        $this->student = User::factory()->create();
        $this->student->roles()->attach($role);

        $this->course = Course::create(['name' => 'UPSC GS', 'slug' => 'upsc-gs', 'is_active' => true]);
        $category = Category::create(['course_id' => $this->course->id, 'name' => 'History', 'slug' => 'history', 'is_active' => true]);

        $this->q1 = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $category->id,
            'question_text' => 'Question 1',
            'status' => 'PUBLISHED',
        ]);
        $this->q1OptA = QuestionOption::create([
            'question_id' => $this->q1->id,
            'option_key' => 'A',
            'option_text' => 'Opt A',
            'is_correct' => true,
        ]);
        $this->q1OptB = QuestionOption::create([
            'question_id' => $this->q1->id,
            'option_key' => 'B',
            'option_text' => 'Opt B',
            'is_correct' => false,
        ]);

        $this->q2 = Question::create([
            'course_id' => $this->course->id,
            'category_id' => $category->id,
            'question_text' => 'Question 2',
            'status' => 'PUBLISHED',
        ]);
        $this->q2OptA = QuestionOption::create([
            'question_id' => $this->q2->id,
            'option_key' => 'A',
            'option_text' => 'Opt A2',
            'is_correct' => true,
        ]);
    }

    public function test_cannot_advance_to_next_question_without_submitting_answer()
    {
        Livewire::actingAs($this->student)
            ->test(PracticeEngine::class, ['course' => $this->course->id])
            ->assertSet('currentIndex', 0)
            ->assertSet('hasSubmitted', false)
            ->call('nextQuestion')
            ->assertSet('currentIndex', 0); // Stays at 0 because answer is not submitted
    }

    public function test_cannot_jump_forward_past_unsubmitted_question()
    {
        Livewire::actingAs($this->student)
            ->test(PracticeEngine::class, ['course' => $this->course->id])
            ->assertSet('currentIndex', 0)
            ->assertSet('hasSubmitted', false)
            ->call('jumpToQuestion', 1)
            ->assertSet('currentIndex', 0); // Stays at 0
    }

    public function test_submitting_answer_enables_next_question_navigation()
    {
        Livewire::actingAs($this->student)
            ->test(PracticeEngine::class, ['course' => $this->course->id])
            ->assertSet('currentIndex', 0)
            ->assertSet('hasSubmitted', false)
            ->call('selectOption', $this->q1OptA->id)
            ->assertSet('hasSubmitted', true)
            ->call('nextQuestion')
            ->assertSet('currentIndex', 1) // Successfully moves to Question 2
            ->assertSet('hasSubmitted', false); // Question 2 is unsubmitted
    }
}
