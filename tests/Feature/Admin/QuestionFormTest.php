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

    public function test_math_expressions_in_question_and_options_are_saved_exactly(): void
    {
        $role = Role::create(['name' => 'ADMIN', 'display_name' => 'Admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role);

        $course = Course::create(['name' => 'Maths', 'slug' => 'maths']);
        $category = Category::create(['course_id' => $course->id, 'name' => 'Indices', 'slug' => 'indices']);

        $questionHtml = '<p><span class="ql-formula" data-value="\left(\frac{1}{216}\right)^{-2/3}+\sqrt[3]{24389}">\left(\frac{1}{216}\right)^{-2/3}+\sqrt[3]{24389}</span> ची किंमत काढा. 2<sup>2</sup>×2<sup>3</sup> ÷ √(a + b)²</p>';
        $optionHtml = '<p><span class="ql-formula" data-value="\frac{5}{100}">\frac{5}{100}</span> ≥ 1:5</p>';

        Livewire::actingAs($admin)
            ->test(QuestionForm::class)
            ->set('course_id', $course->id)
            ->set('category_id', $category->id)
            ->set('question_html', $questionHtml)
            ->set('options', [
                ['uid' => 'opt_1', 'option_key' => 'A', 'option_html' => $optionHtml, 'is_correct' => true],
                ['uid' => 'opt_2', 'option_key' => 'B', 'option_html' => '<p>2<sup>4</sup></p>', 'is_correct' => false],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $question = Question::first();
        $this->assertSame($questionHtml, $question->question_html);
        $this->assertSame('$\left(\frac{1}{216}\right)^{-2/3}+\sqrt[3]{24389}$ ची किंमत काढा. 2^2×2^3 ÷ √(a + b)²', $question->question_text);

        $options = $question->options()->orderBy('sort_order')->get();
        $this->assertSame($optionHtml, $options[0]->option_html);
        $this->assertSame('$\frac{5}{100}$ ≥ 1:5', $options[0]->option_text);
        $this->assertSame('2^4', $options[1]->option_text);
    }

    public function test_quick_templates_format_and_clear_button_present(): void
    {
        $role = Role::create(['name' => 'ADMIN', 'display_name' => 'Admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role);

        $course = Course::create(['name' => 'Maths', 'slug' => 'maths']);
        Category::create(['course_id' => $course->id, 'name' => 'Algebra', 'slug' => 'algebra']);

        Livewire::actingAs($admin)
            ->test(QuestionForm::class)
            ->assertSee('Clear Formula')
            ->assertSee('(2²×2³)/(2²×2³) (Fraction with Powers)')
            ->assertSee('(1/216)⁻²⁄³ + (1/256)⁻³⁄⁴ + (1/243)⁻¹⁄³ (Fraction Powers Sum)')
            ->assertSee('√(a+b)² (Root of Expression)')
            ->assertSee('∛24389 (Cube Root of Number)')
            ->assertSee('[(2⁴)¹⁄²]¹ (Nested Powers)')
            ->assertSee('x = (-b±√(b²-4ac))/2a (Quadratic Formula)')
            ->assertSee('sin²θ + cos²θ = 1 (Trig Identity)')
            ->assertDontSee('(Q41)')
            ->assertDontSee('(Q42)')
            ->assertDontSee('(Q44)')
            ->assertDontSee('(Q45)')
            ->assertDontSee('(Q47)');
    }
}
