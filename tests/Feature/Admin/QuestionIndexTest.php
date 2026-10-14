<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Questions\QuestionIndex;
use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuestionIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Course $course1;

    protected Course $course2;

    protected Category $category1;

    protected Subcategory $subcategory1;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'ADMIN'], ['display_name' => 'Admin']);
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($role);

        $this->course1 = Course::create(['name' => 'MPSC State Services', 'slug' => 'mpsc-state-services', 'is_active' => true]);
        $this->course2 = Course::create(['name' => 'UPSC Civil Services', 'slug' => 'upsc-civil-services', 'is_active' => true]);

        $this->category1 = Category::create([
            'course_id' => $this->course1->id,
            'name' => 'History of Maharashtra',
            'slug' => 'history-maharashtra',
            'is_active' => true,
        ]);

        $this->subcategory1 = Subcategory::create([
            'category_id' => $this->category1->id,
            'name' => 'Ancient Period',
            'slug' => 'ancient-period',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_questions_index_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.questions.index'));

        $response->assertStatus(200);
        $response->assertSee('Questions Bank');
        $response->assertSee('Keyword Search');
        $response->assertSee('Course / Stream');
        $response->assertSee('MPSC State Services');
    }

    public function test_can_search_questions_by_keyword_text_and_code(): void
    {
        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $this->category1->id,
            'question_number' => 1,
            'question_code' => 'MPSC-HIST-0001',
            'question_text' => 'Who was the founder of Satavahana dynasty?',
            'question_html' => '<p>Who was the founder of Satavahana dynasty?</p>',
            'difficulty' => 'Medium',
            'status' => 'PUBLISHED',
        ]);

        Question::create([
            'course_id' => $this->course2->id,
            'category_id' => $this->category1->id,
            'question_number' => 2,
            'question_code' => 'UPSC-GEO-0002',
            'question_text' => 'What is the highest mountain peak in the Western Ghats?',
            'question_html' => '<p>What is the highest mountain peak in the Western Ghats?</p>',
            'difficulty' => 'Easy',
            'status' => 'PUBLISHED',
        ]);

        // Search by question text keyword
        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('search', 'Satavahana')
            ->assertSee('MPSC-HIST-0001')
            ->assertDontSee('UPSC-GEO-0002');

        // Search by question code
        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('search', 'UPSC-GEO-0002')
            ->assertSee('highest mountain peak')
            ->assertDontSee('Satavahana');
    }

    public function test_can_filter_questions_by_course(): void
    {
        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $this->category1->id,
            'question_code' => 'MPSC-Q-1',
            'question_text' => 'MPSC Specific Question',
            'difficulty' => 'Medium',
            'status' => 'PUBLISHED',
        ]);

        Question::create([
            'course_id' => $this->course2->id,
            'category_id' => $this->category1->id,
            'question_code' => 'UPSC-Q-2',
            'question_text' => 'UPSC Specific Question',
            'difficulty' => 'Hard',
            'status' => 'PUBLISHED',
        ]);

        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('course_id', (string) $this->course1->id)
            ->assertSee('MPSC Specific Question')
            ->assertDontSee('UPSC Specific Question');

        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('course_id', (string) $this->course2->id)
            ->assertSee('UPSC Specific Question')
            ->assertDontSee('MPSC Specific Question');
    }

    public function test_can_filter_by_cascading_category_and_subcategory(): void
    {
        $category2 = Category::create([
            'course_id' => $this->course1->id,
            'name' => 'Geography of Maharashtra',
            'slug' => 'geo-maha',
            'is_active' => true,
        ]);

        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $this->category1->id,
            'subcategory_id' => $this->subcategory1->id,
            'question_code' => 'MPSC-HIST-ANC-1',
            'question_text' => 'Ancient Maharashtra History Question',
            'difficulty' => 'Medium',
            'status' => 'PUBLISHED',
        ]);

        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $category2->id,
            'question_code' => 'MPSC-GEO-1',
            'question_text' => 'Maharashtra River Systems Question',
            'difficulty' => 'Medium',
            'status' => 'PUBLISHED',
        ]);

        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('course_id', (string) $this->course1->id)
            ->set('category_id', (string) $this->category1->id)
            ->assertSee('Ancient Maharashtra History Question')
            ->assertDontSee('Maharashtra River Systems Question')
            ->set('subcategory_id', (string) $this->subcategory1->id)
            ->assertSee('Ancient Maharashtra History Question');
    }

    public function test_changing_course_resets_category_and_subcategory(): void
    {
        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('course_id', (string) $this->course1->id)
            ->set('category_id', (string) $this->category1->id)
            ->set('subcategory_id', (string) $this->subcategory1->id)
            ->assertSet('category_id', (string) $this->category1->id)
            ->assertSet('subcategory_id', (string) $this->subcategory1->id)
            ->set('course_id', (string) $this->course2->id)
            ->assertSet('category_id', '')
            ->assertSet('subcategory_id', '');
    }

    public function test_can_filter_questions_by_difficulty(): void
    {
        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $this->category1->id,
            'question_code' => 'DIFF-EASY-1',
            'question_text' => 'Easy Difficulty Question',
            'difficulty' => 'Easy',
            'status' => 'PUBLISHED',
        ]);

        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $this->category1->id,
            'question_code' => 'DIFF-HARD-1',
            'question_text' => 'Hard Difficulty Question',
            'difficulty' => 'Hard',
            'status' => 'PUBLISHED',
        ]);

        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('difficulty', 'Easy')
            ->assertSee('Easy Difficulty Question')
            ->assertDontSee('Hard Difficulty Question')
            ->set('difficulty', 'Hard')
            ->assertSee('Hard Difficulty Question')
            ->assertDontSee('Easy Difficulty Question');
    }

    public function test_can_filter_questions_by_status(): void
    {
        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $this->category1->id,
            'question_code' => 'STATUS-DRAFT-1',
            'question_text' => 'Draft Question Title',
            'difficulty' => 'Medium',
            'status' => 'DRAFT',
        ]);

        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $this->category1->id,
            'question_code' => 'STATUS-REV-1',
            'question_text' => 'Review Needed Question',
            'difficulty' => 'Medium',
            'status' => 'REVIEW',
        ]);

        Question::create([
            'course_id' => $this->course1->id,
            'category_id' => $this->category1->id,
            'question_code' => 'STATUS-PUB-1',
            'question_text' => 'Published Question Title',
            'difficulty' => 'Medium',
            'status' => 'PUBLISHED',
        ]);

        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('status', 'DRAFT')
            ->assertSee('Draft Question Title')
            ->assertDontSee('Published Question Title')
            ->assertDontSee('Review Needed Question')
            ->set('status', 'REVIEW')
            ->assertSee('Review Needed Question')
            ->assertDontSee('Draft Question Title')
            ->assertDontSee('Published Question Title')
            ->set('status', 'PUBLISHED')
            ->assertSee('Published Question Title')
            ->assertDontSee('Draft Question Title')
            ->assertDontSee('Review Needed Question');
    }

    public function test_reset_filters_clears_all_criteria(): void
    {
        Livewire::actingAs($this->admin)
            ->test(QuestionIndex::class)
            ->set('search', 'Sample query')
            ->set('course_id', (string) $this->course1->id)
            ->set('category_id', (string) $this->category1->id)
            ->set('subcategory_id', (string) $this->subcategory1->id)
            ->set('difficulty', 'Hard')
            ->set('status', 'REVIEW')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('course_id', '')
            ->assertSet('category_id', '')
            ->assertSet('subcategory_id', '')
            ->assertSet('difficulty', '')
            ->assertSet('status', '');
    }
}
