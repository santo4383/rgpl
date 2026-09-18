<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Courses
        $mpsc = Course::firstOrCreate([
            'name' => 'MPSC State Services (Rajyaseva)',
        ], [
            'description' => 'Comprehensive preparation for Maharashtra Public Service Commission preliminary and main exams.',
            'is_active' => true,
            'sort_order' => 1,
            'free_question_limit' => 500,
        ]);

        $police = Course::firstOrCreate([
            'name' => 'Maharashtra Police Bharti',
        ], [
            'description' => 'Targeted MCQ practice for Maharashtra Police Constable and Sub-Inspector recruitment examinations.',
            'is_active' => true,
            'sort_order' => 2,
            'free_question_limit' => 300,
        ]);

        // Enroll existing users in both courses
        $users = User::all();
        foreach ($users as $u) {
            $u->courses()->syncWithoutDetaching([$mpsc->id, $police->id]);
        }

        // 2. Categories & Subcategories for MPSC
        $polity = Category::firstOrCreate([
            'course_id' => $mpsc->id,
            'name' => 'Indian Polity & Governance',
        ], [
            'description' => 'Constitution of India, Fundamental Rights, Directive Principles, and Parliamentary System.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $fundRights = Subcategory::firstOrCreate([
            'category_id' => $polity->id,
            'name' => 'Fundamental Rights (Articles 12-35)',
        ], [
            'description' => 'Part III of the Indian Constitution.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $history = Category::firstOrCreate([
            'course_id' => $mpsc->id,
            'name' => 'History & Social Reformers of Maharashtra',
        ], [
            'description' => 'Modern History of Maharashtra and Great Social Reformers.',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $science = Category::firstOrCreate([
            'course_id' => $mpsc->id,
            'name' => 'General Science & Technology',
        ], [
            'description' => 'Physics, Chemistry, and Biology basics.',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // 3. Categories for Police Bharti
        $gk = Category::firstOrCreate([
            'course_id' => $police->id,
            'name' => 'General Knowledge & Current Affairs',
        ], [
            'description' => 'National and State General Knowledge.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $maths = Category::firstOrCreate([
            'course_id' => $police->id,
            'name' => 'Basic Mathematics & Mental Ability',
        ], [
            'description' => 'Arithmetic and numerical reasoning.',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // 4. Sample Questions
        $questionsData = [
            [
                'course_id' => $mpsc->id,
                'category_id' => $polity->id,
                'subcategory_id' => $fundRights->id,
                'question_html' => '<p>Which Article of the Indian Constitution guarantees the <strong>Right to Constitutional Remedies</strong>, often referred to by Dr. B.R. Ambedkar as the <em>"Heart and Soul of the Constitution"</em>?</p>',
                'question_text' => 'Which Article of the Indian Constitution guarantees the Right to Constitutional Remedies, often referred to by Dr. B.R. Ambedkar as the "Heart and Soul of the Constitution"?',
                'explanation_html' => '<p><strong>Article 32</strong> guarantees the right to move the Supreme Court by appropriate proceedings for the enforcement of the rights conferred by Part III. Dr. B.R. Ambedkar called it the heart and soul of the Constitution because fundamental rights would be meaningless without an effective judicial remedy.</p>',
                'explanation_text' => 'Article 32 guarantees the right to move the Supreme Court for enforcement of fundamental rights.',
                'difficulty' => 'Medium',
                'status' => 'PUBLISHED',
                'is_free' => true,
                'options' => [
                    ['option_key' => 'A', 'text' => 'Article 19', 'html' => '<p>Article 19</p>', 'is_correct' => false],
                    ['option_key' => 'B', 'text' => 'Article 21', 'html' => '<p>Article 21</p>', 'is_correct' => false],
                    ['option_key' => 'C', 'text' => 'Article 32', 'html' => '<p>Article 32</p>', 'is_correct' => true],
                    ['option_key' => 'D', 'text' => 'Article 226', 'html' => '<p>Article 226</p>', 'is_correct' => false],
                ],
            ],
            [
                'course_id' => $mpsc->id,
                'category_id' => $history->id,
                'subcategory_id' => null,
                'question_html' => '<p>In which year did <strong>Mahatma Jyotirao Phule</strong> establish the <em>Satyashodhak Samaj</em> in Pune?</p>',
                'question_text' => 'In which year did Mahatma Jyotirao Phule establish the Satyashodhak Samaj in Pune?',
                'explanation_html' => '<p>Mahatma Jyotirao Phule founded the <strong>Satyashodhak Samaj</strong> (Truth-Seekers\' Society) on <strong>September 24, 1873</strong>, with the objective of liberating the Shudras and Ati-Shudras from exploitation.</p>',
                'explanation_text' => 'Founded on September 24, 1873 in Pune.',
                'difficulty' => 'Easy',
                'status' => 'PUBLISHED',
                'is_free' => true,
                'options' => [
                    ['option_key' => 'A', 'text' => '1848', 'html' => '<p>1848</p>', 'is_correct' => false],
                    ['option_key' => 'B', 'text' => '1873', 'html' => '<p>1873</p>', 'is_correct' => true],
                    ['option_key' => 'C', 'text' => '1885', 'html' => '<p>1885</p>', 'is_correct' => false],
                    ['option_key' => 'D', 'text' => '1890', 'html' => '<p>1890</p>', 'is_correct' => false],
                ],
            ],
            [
                'course_id' => $mpsc->id,
                'category_id' => $science->id,
                'subcategory_id' => null,
                'question_html' => '<p>What is the SI unit of <strong>Electric Resistance</strong>?</p>',
                'question_text' => 'What is the SI unit of Electric Resistance?',
                'explanation_html' => '<p>The SI unit of electrical resistance is the <strong>Ohm (&Omega;)</strong>, named after German physicist Georg Simon Ohm.</p>',
                'explanation_text' => 'The SI unit of electrical resistance is the Ohm.',
                'difficulty' => 'Easy',
                'status' => 'PUBLISHED',
                'is_free' => true,
                'options' => [
                    ['option_key' => 'A', 'text' => 'Volt', 'html' => '<p>Volt</p>', 'is_correct' => false],
                    ['option_key' => 'B', 'text' => 'Ampere', 'html' => '<p>Ampere</p>', 'is_correct' => false],
                    ['option_key' => 'C', 'text' => 'Ohm', 'html' => '<p>Ohm</p>', 'is_correct' => true],
                    ['option_key' => 'D', 'text' => 'Watt', 'html' => '<p>Watt</p>', 'is_correct' => false],
                ],
            ],
            [
                'course_id' => $police->id,
                'category_id' => $gk->id,
                'subcategory_id' => null,
                'question_html' => '<p>Where is the <strong>Maharashtra Police Academy (MPA)</strong> located in Maharashtra?</p>',
                'question_text' => 'Where is the Maharashtra Police Academy (MPA) located in Maharashtra?',
                'explanation_html' => '<p>The <strong>Maharashtra Police Academy (MPA)</strong> is located in <strong>Nashik</strong>. It was established in 1906 as a Police Training School and later upgraded to an academy in 1990.</p>',
                'explanation_text' => 'Maharashtra Police Academy is located in Nashik.',
                'difficulty' => 'Easy',
                'status' => 'PUBLISHED',
                'is_free' => true,
                'options' => [
                    ['option_key' => 'A', 'text' => 'Pune', 'html' => '<p>Pune</p>', 'is_correct' => false],
                    ['option_key' => 'B', 'text' => 'Mumbai', 'html' => '<p>Mumbai</p>', 'is_correct' => false],
                    ['option_key' => 'C', 'text' => 'Nashik', 'html' => '<p>Nashik</p>', 'is_correct' => true],
                    ['option_key' => 'D', 'text' => 'Nagpur', 'html' => '<p>Nagpur</p>', 'is_correct' => false],
                ],
            ],
            [
                'course_id' => $police->id,
                'category_id' => $maths->id,
                'subcategory_id' => null,
                'question_html' => '<p>If a train travelling at <strong>72 km/h</strong> crosses a pole in <strong>15 seconds</strong>, what is the length of the train in metres?</p>',
                'question_text' => 'If a train travelling at 72 km/h crosses a pole in 15 seconds, what is the length of the train in metres?',
                'explanation_html' => '<p>First, convert speed from km/h to m/s:<br>Speed = 72 &times; (5 / 18) = <strong>20 m/s</strong>.<br>Distance (Length of train) = Speed &times; Time = 20 m/s &times; 15 s = <strong>300 metres</strong>.</p>',
                'explanation_text' => 'Speed = 72 * 5/18 = 20 m/s. Length = 20 * 15 = 300 metres.',
                'difficulty' => 'Medium',
                'status' => 'PUBLISHED',
                'is_free' => true,
                'options' => [
                    ['option_key' => 'A', 'text' => '250 m', 'html' => '<p>250 m</p>', 'is_correct' => false],
                    ['option_key' => 'B', 'text' => '300 m', 'html' => '<p>300 m</p>', 'is_correct' => true],
                    ['option_key' => 'C', 'text' => '350 m', 'html' => '<p>350 m</p>', 'is_correct' => false],
                    ['option_key' => 'D', 'text' => '400 m', 'html' => '<p>400 m</p>', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($questionsData as $qData) {
            $options = $qData['options'];
            unset($qData['options']);

            $existing = Question::where('course_id', $qData['course_id'])
                ->where('question_text', $qData['question_text'])
                ->first();

            if (! $existing) {
                $question = Question::create($qData);
                foreach ($options as $idx => $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_key' => $opt['option_key'],
                        'option_text' => $opt['text'],
                        'option_html' => $opt['html'],
                        'is_correct' => $opt['is_correct'],
                        'sort_order' => $idx,
                    ]);
                }
            }
        }
    }
}
