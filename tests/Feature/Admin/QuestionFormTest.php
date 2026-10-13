<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Questions\QuestionForm;
use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuestionFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_question_with_multiple_correct_options_and_auto_code()
    {
        $role = Role::create(['name' => 'ADMIN', 'display_name' => 'Admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role);

        $course = Course::create(['name' => 'UPSC CSAT', 'slug' => 'upsc-csat']);
        $category = Category::create(['course_id' => $course->id, 'name' => 'Quant Aptitude', 'slug' => 'quant-aptitude']);
        $subcategory = Subcategory::create(['category_id' => $category->id, 'name' => 'Percentage', 'slug' => 'percentage']);

        Livewire::actingAs($admin)
            ->test(QuestionForm::class)
            ->set('course_id', $course->id)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('question_html', '<p>Sample Question with multiple correct options?</p>')
            ->set('options', [
                ['uid' => 'opt_1', 'option_key' => 'A', 'option_html' => 'Option A', 'is_correct' => true],
                ['uid' => 'opt_2', 'option_key' => 'B', 'option_html' => 'Option B', 'is_correct' => false],
                ['uid' => 'opt_3', 'option_key' => 'C', 'option_html' => 'Option C', 'is_correct' => true],
                ['uid' => 'opt_4', 'option_key' => 'D', 'option_html' => 'Option D', 'is_correct' => false],
            ])
            ->call('save')
            ->assertRedirect(route('admin.questions.index'));

        $this->assertDatabaseHas('questions', [
            'course_id' => $course->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'question_number' => 1,
            'question_code' => 'UPSC-CSAT-QUANT-APTI-PERCENTAGE-0001',
        ]);

        $question = Question::first();
        $this->assertCount(4, $question->options);
        $correctOptions = $question->options->where('is_correct', true)->pluck('option_key')->toArray();
        $this->assertEquals(['A', 'C'], array_values($correctOptions));
    }

    public function test_question_number_increments_for_same_hierarchy()
    {
        $role = Role::create(['name' => 'ADMIN', 'display_name' => 'Admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role);

        $course = Course::create(['name' => 'UPSC CSAT', 'slug' => 'upsc-csat']);
        $category = Category::create(['course_id' => $course->id, 'name' => 'Quant Aptitude', 'slug' => 'quant-aptitude']);

        Question::create([
            'course_id' => $course->id,
            'category_id' => $category->id,
            'question_number' => 1,
            'question_code' => 'UPSC-CSAT-QUANT-APTI-0001',
            'question_text' => 'First Q',
            'status' => 'PUBLISHED',
        ]);

        Livewire::actingAs($admin)
            ->test(QuestionForm::class)
            ->set('course_id', $course->id)
            ->set('category_id', $category->id)
            ->set('question_html', '<p>Second Question?</p>')
            ->set('options', [
                ['uid' => 'opt_1', 'option_key' => 'A', 'option_html' => 'Option A', 'is_correct' => true],
                ['uid' => 'opt_2', 'option_key' => 'B', 'option_html' => 'Option B', 'is_correct' => false],
            ])
            ->call('save');

        $this->assertDatabaseHas('questions', [
            'question_number' => 2,
            'question_code' => 'UPSC-CSAT-QUANT-APTI-0002',
        ]);

        $secondQ = Question::where('question_number', 2)->first();
        $this->assertEquals('Second Question?', $secondQ->question_text);
    }
}
