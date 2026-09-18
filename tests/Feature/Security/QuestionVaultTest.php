<?php

namespace Tests\Feature\Security;

use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\Security\QuestionVaultService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuestionVaultTest extends TestCase
{
    use RefreshDatabase;

    public function test_vault_encrypts_and_decrypts_strings_correctly(): void
    {
        $vault = app(QuestionVaultService::class);
        $original = 'What is the capital of Maharashtra?';

        $encrypted = $vault->encrypt($original);

        $this->assertNotEquals($original, $encrypted);
        $this->assertTrue(str_starts_with($encrypted, 'vault:v1:'));

        $decrypted = $vault->decrypt($encrypted);
        $this->assertEquals($original, $decrypted);
    }

    public function test_question_model_stores_encrypted_text_in_database(): void
    {
        $course = Course::create([
            'name' => 'MPSC State Services',
            'is_active' => true,
        ]);

        $category = Category::create([
            'course_id' => $course->id,
            'name' => 'Polity',
            'is_active' => true,
        ]);

        $question = Question::create([
            'course_id' => $course->id,
            'category_id' => $category->id,
            'question_text' => 'Confidential Exam Question on Constitutional Law',
            'question_html' => '<p>Confidential Exam Question on Constitutional Law</p>',
            'explanation_text' => 'Confidential detailed explanation',
            'difficulty' => 'Hard',
            'status' => 'PUBLISHED',
        ]);

        // Raw database check
        $raw = DB::table('questions')->where('id', $question->id)->first();
        $this->assertTrue(str_starts_with($raw->question_text, 'vault:v1:'));
        $this->assertStringNotContainsString('Confidential Exam Question', $raw->question_text);

        // Eloquent access check
        $fresh = Question::find($question->id);
        $this->assertEquals('Confidential Exam Question on Constitutional Law', $fresh->question_text);
        $this->assertEquals('Confidential detailed explanation', $fresh->explanation_text);
    }

    public function test_question_option_stores_encrypted_text_in_database(): void
    {
        $course = Course::create([
            'name' => 'Police Bharti',
            'is_active' => true,
        ]);

        $category = Category::create([
            'course_id' => $course->id,
            'name' => 'Maths',
            'is_active' => true,
        ]);

        $question = Question::create([
            'course_id' => $course->id,
            'category_id' => $category->id,
            'question_text' => 'Sample Math Question',
            'status' => 'PUBLISHED',
        ]);

        $opt = QuestionOption::create([
            'question_id' => $question->id,
            'option_key' => 'A',
            'option_text' => 'Confidential Option Value 42',
            'option_html' => '<p>Confidential Option Value 42</p>',
            'is_correct' => true,
        ]);

        // Raw DB check
        $rawOpt = DB::table('question_options')->where('id', $opt->id)->first();
        $this->assertTrue(str_starts_with($rawOpt->option_text, 'vault:v1:'));
        $this->assertStringNotContainsString('Confidential Option Value', $rawOpt->option_text);

        // Eloquent check
        $freshOpt = QuestionOption::find($opt->id);
        $this->assertEquals('Confidential Option Value 42', $freshOpt->option_text);
    }
}
