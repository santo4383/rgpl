<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Course;
use App\Models\Faq;
use App\Models\HomepageSlider;
use App\Models\HomepageStatistic;
use App\Models\NewsArticle;
use App\Models\Page;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Models\Testimonial;
use App\Services\PublicContentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ParikshaMitraContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Settings Updates
        Setting::set('site_name', 'ParikshaMitra', 'general', 'string');
        Setting::set('tagline', 'Your Exam Preparation Partner', 'general', 'string');
        Setting::set('contact_email', 'support@parikshamitra.com', 'general', 'string');
        Setting::set('contact_phone', '+91 98765 43210', 'general', 'string');
        Setting::set('contact_address', 'ParikshaMitra Learning Technologies, FC Road, Shivaji Nagar, Pune, Maharashtra 411005', 'general', 'string');
        Setting::set('social_facebook', 'https://facebook.com/ParikshaMitraOfficial', 'social', 'string');
        Setting::set('social_instagram', 'https://instagram.com/parikshamitra', 'social', 'string');
        Setting::set('social_youtube', 'https://youtube.com/@ParikshaMitra', 'social', 'string');
        Setting::set('social_telegram', 'https://t.me/ParikshaMitraExams', 'social', 'string');

        // 2. Homepage Sliders
        $sliders = [
            [
                'title' => 'Prepare Smarter. Practice More. Score Better.',
                'subtitle' => 'Your preparation partner for MPSC, Police Bharti and Commerce examinations.',
                'description' => 'Comprehensive question bank with Marathi & English bilingual support, timed mock exams, and in-depth conceptual explanations.',
                'desktop_image' => null,
                'mobile_image' => null,
                'button_text' => 'Start Practicing',
                'button_url' => '/practice',
                'secondary_button_text' => 'Explore Courses',
                'secondary_button_url' => '/courses',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Start Your Preparation with 500 Free Questions',
                'subtitle' => 'Zero risk, immediate access to high-yield competitive exam question sets.',
                'description' => 'Experience the adaptive practice engine, track your subject-wise accuracy, and unlock detailed answer explanations for free.',
                'desktop_image' => null,
                'mobile_image' => null,
                'button_text' => 'Register & Practice Free',
                'button_url' => '/register',
                'secondary_button_text' => 'View Pricing Plans',
                'secondary_button_url' => '/pricing',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Prepare for MPSC with Focused MCQ Practice',
                'subtitle' => 'Targeted practice for Rajyaseva, Group B, Group C, and Combined Prelims.',
                'description' => 'Polity, Maharashtra History, Geography, General Science, and CSAT with authentic examination syllabus alignment.',
                'desktop_image' => null,
                'mobile_image' => null,
                'button_text' => 'Explore MPSC Field',
                'button_url' => '/courses/mpsc',
                'secondary_button_text' => 'Take Mock Test',
                'secondary_button_url' => '/exams',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Build Strong Commerce Fundamentals',
                'subtitle' => 'Structured question modules for 11th & 12th Commerce, CA, CS, and ICWA.',
                'description' => 'Master Double Entry Bookkeeping, Mercantile Law, Cost Accounting, and Taxation with chapter-level practice.',
                'desktop_image' => null,
                'mobile_image' => null,
                'button_text' => 'Explore Commerce Stream',
                'button_url' => '/courses/commerce',
                'secondary_button_text' => 'See Study Modules',
                'secondary_button_url' => '/courses/commerce',
                'display_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $s) {
            HomepageSlider::updateOrCreate(['title' => $s['title']], $s);
        }

        // 3. Courses / Preparation Areas
        $mpsc = Course::updateOrCreate(['name' => 'MPSC State Services (Rajyaseva)'], [
            'slug' => 'mpsc',
            'description' => 'Comprehensive preparation for Maharashtra Public Service Commission preliminary and main examinations.',
            'icon' => 'shield-check',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
            'free_question_limit' => 500,
        ]);

        $police = Course::updateOrCreate(['name' => 'Maharashtra Police Bharti'], [
            'slug' => 'police-bharti',
            'description' => 'Targeted MCQ practice for Maharashtra Police Constable, SRPF, and Driver recruitment examinations.',
            'icon' => 'badge-check',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
            'free_question_limit' => 300,
        ]);

        $commerce = Course::updateOrCreate(['name' => 'Commerce & Professional Studies'], [
            'slug' => 'commerce',
            'description' => 'Concept-building MCQ practice covering 11th & 12th State Board Commerce, CA Foundation, CS Executive, and ICWA/CMA.',
            'icon' => 'chart-bar',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
            'free_question_limit' => 500,
        ]);

        // Commerce Categories (11th Commerce, 12th Commerce, CA, CS, ICWA)
        $comCategories = [
            [
                'name' => '11th Commerce',
                'slug' => '11th-commerce',
                'description' => 'Foundation curriculum for FYJC Commerce students: Bookkeeping, Economics, and Secretarial Practice.',
                'is_featured' => true,
                'sort_order' => 1,
                'subcategories' => [
                    'Bookkeeping & Accountancy',
                    'Economics & Indian Economy',
                    'Organization of Commerce & Management',
                    'Secretarial Practice (SP)',
                ],
            ],
            [
                'name' => '12th Commerce',
                'slug' => '12th-commerce',
                'description' => 'HSC Board examination-focused practice modules with step-wise accounting and theory questions.',
                'is_featured' => true,
                'sort_order' => 2,
                'subcategories' => [
                    'Partnership Final Accounts & NPO',
                    'Company Accounts & Issue of Shares',
                    'Macroeconomics & Public Finance',
                    'Principles of Management',
                ],
            ],
            [
                'name' => 'CA (Chartered Accountancy)',
                'slug' => 'ca',
                'description' => 'CA Foundation & Intermediate preparation bank for ICAI aspirants with high-difficulty conceptual MCQs.',
                'is_featured' => true,
                'sort_order' => 3,
                'subcategories' => [
                    'Principles & Practice of Accounting',
                    'Business Laws & Business Correspondence',
                    'Business Mathematics & Logical Reasoning',
                    'Business Economics & BCK',
                ],
            ],
            [
                'name' => 'CS (Company Secretary)',
                'slug' => 'cs',
                'description' => 'CSEET and Executive program MCQ series covering Corporate Laws, Governance, and Jurisprudence.',
                'is_featured' => true,
                'sort_order' => 4,
                'subcategories' => [
                    'Business Communication & Legal Aptitude',
                    'Economic & Commercial Laws',
                    'Current Affairs & Presentation Skills',
                ],
            ],
            [
                'name' => 'ICWA / CMA',
                'slug' => 'icwa',
                'description' => 'Cost and Management Accounting foundation & intermediate modules with practical calculations.',
                'is_featured' => true,
                'sort_order' => 5,
                'subcategories' => [
                    'Fundamentals of Financial Accounting',
                    'Fundamentals of Commercial Laws',
                    'Cost Accounting & Material Management',
                    'Direct & Indirect Taxation',
                ],
            ],
        ];

        foreach ($comCategories as $catData) {
            $subs = $catData['subcategories'];
            unset($catData['subcategories']);

            $cat = Category::updateOrCreate([
                'course_id' => $commerce->id,
                'name' => $catData['name'],
            ], array_merge($catData, ['is_active' => true]));

            foreach ($subs as $sIdx => $subName) {
                Subcategory::updateOrCreate([
                    'category_id' => $cat->id,
                    'name' => $subName,
                ], [
                    'slug' => Str::slug($subName),
                    'description' => "Practice questions and topics for {$subName}.",
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => $sIdx + 1,
                ]);
            }
        }

        // Add questions for 11th and 12th commerce demo
        $firstCat = Category::where('course_id', $commerce->id)->first();
        $firstSub = Subcategory::where('category_id', $firstCat->id)->first();

        if ($firstCat && $firstSub) {
            $q = Question::updateOrCreate([
                'course_id' => $commerce->id,
                'question_text' => 'According to the Dual Aspect concept of Accounting, which of the following equations is always correct?',
            ], [
                'category_id' => $firstCat->id,
                'subcategory_id' => $firstSub->id,
                'question_html' => '<p>According to the <strong>Dual Aspect concept</strong> of Accounting, which of the following fundamental equations is always correct?</p>',
                'explanation_html' => '<p>The fundamental accounting equation states: <strong>Assets = Liabilities + Capital (Owner’s Equity)</strong>. Every business transaction impacts at least two accounts, keeping this equation in balance.</p>',
                'explanation_text' => 'Assets = Liabilities + Capital is the fundamental accounting equation.',
                'difficulty' => 'Easy',
                'status' => 'PUBLISHED',
                'is_free' => true,
            ]);

            QuestionOption::updateOrCreate(['question_id' => $q->id, 'option_key' => 'A'], [
                'option_text' => 'Assets = Liabilities - Capital',
                'option_html' => '<p>Assets = Liabilities - Capital</p>',
                'is_correct' => false,
                'sort_order' => 1,
            ]);
            QuestionOption::updateOrCreate(['question_id' => $q->id, 'option_key' => 'B'], [
                'option_text' => 'Assets = Liabilities + Capital',
                'option_html' => '<p>Assets = Liabilities + Capital</p>',
                'is_correct' => true,
                'sort_order' => 2,
            ]);
            QuestionOption::updateOrCreate(['question_id' => $q->id, 'option_key' => 'C'], [
                'option_text' => 'Capital = Assets + Liabilities',
                'option_html' => '<p>Capital = Assets + Liabilities</p>',
                'is_correct' => false,
                'sort_order' => 3,
            ]);
            QuestionOption::updateOrCreate(['question_id' => $q->id, 'option_key' => 'D'], [
                'option_text' => 'Liabilities = Assets + Capital',
                'option_html' => '<p>Liabilities = Assets + Capital</p>',
                'is_correct' => false,
                'sort_order' => 4,
            ]);
        }

        // 4. Homepage Statistics
        $stats = [
            [
                'title' => 'Total Questions',
                'value' => '10,000+',
                'stat_key' => 'total_questions',
                'icon' => 'document-duplicate',
                'description' => 'Curated MCQs with bilingual explanations',
                'display_order' => 1,
                'status' => 'ACTIVE',
            ],
            [
                'title' => 'Registered Students',
                'value' => '5,000+',
                'stat_key' => 'total_students',
                'icon' => 'users',
                'description' => 'Aspirants practicing actively statewide',
                'display_order' => 2,
                'status' => 'ACTIVE',
            ],
            [
                'title' => 'Free Questions',
                'value' => '500+',
                'stat_key' => 'free_questions',
                'icon' => 'sparkles',
                'description' => 'Zero-cost starter quota for every student',
                'display_order' => 3,
                'status' => 'ACTIVE',
            ],
            [
                'title' => 'Preparation Areas',
                'value' => '3',
                'stat_key' => 'total_courses',
                'icon' => 'academic-cap',
                'description' => 'MPSC, Police Bharti & Commerce Streams',
                'display_order' => 4,
                'status' => 'ACTIVE',
            ],
            [
                'title' => 'Mock Exams & Tests',
                'value' => '50+',
                'stat_key' => 'mock_exams',
                'icon' => 'clipboard-check',
                'description' => 'Timed tests with negative marking',
                'display_order' => 5,
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($stats as $stat) {
            HomepageStatistic::updateOrCreate(['title' => $stat['title']], $stat);
        }

        // 5. Announcements
        $announcements = [
            [
                'title' => 'New MPSC Rajyaseva 2026 Prelims Practice Sets Added',
                'short_description' => 'Fresh 200+ Maharashtra Social Reformers & Indian Polity questions are now live for practice.',
                'content' => 'We have published authentic past-trend questions with comprehensive bilingual explanations covering the revised MPSC syllabus pattern.',
                'link' => '/courses/mpsc',
                'button_text' => 'View Details',
                'priority' => 'HIGH',
                'category' => 'Exam Update',
                'status' => 'PUBLISHED',
                'featured' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(30),
            ],
            [
                'title' => 'Police Bharti Special Speed-Mathematics Drill Launched',
                'short_description' => 'Sharpen speed and mental calculation for Police Constable numerical questions.',
                'content' => 'Time-distance, percentages, and reasoning shortcuts tailored specifically for ground-passed candidates appearing for the written test.',
                'link' => '/practice',
                'button_text' => 'Start Drill',
                'priority' => 'MEDIUM',
                'category' => 'Practice Update',
                'status' => 'PUBLISHED',
                'featured' => false,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(20),
            ],
            [
                'title' => 'Commerce Section Now Live: 11th, 12th, CA Foundation & CS',
                'short_description' => 'Full-fledged MCQ series for commerce students and professional finance aspirants.',
                'content' => 'Explore chapter-by-chapter exercises in Bookkeeping, Mercantile Law, and Economics designed to reinforce fundamental concepts.',
                'link' => '/courses/commerce',
                'button_text' => 'Explore Stream',
                'priority' => 'HIGH',
                'category' => 'New Course',
                'status' => 'PUBLISHED',
                'featured' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(45),
            ],
        ];

        foreach ($announcements as $ann) {
            Announcement::updateOrCreate(['title' => $ann['title']], $ann);
        }

        // 6. News Articles
        $articles = [
            [
                'title' => 'MPSC State Services 2026 Notification & Syllabus Breakdown',
                'slug' => 'mpsc-state-services-2026-notification-syllabus-breakdown',
                'short_description' => 'A detailed look at the latest MPSC syllabus revisions, weightage of GS papers, and winning MCQ practice strategies.',
                'content_html' => '<p>The Maharashtra Public Service Commission (MPSC) examination demands both in-depth conceptual clarity and rapid elimination skills during the preliminary objective test. Aspirants preparing for the upcoming cycle must adopt a dual strategy: textbook revision coupled with rigorous daily MCQ solving.</p><h3>Key Focus Areas for Paper 1 (General Studies)</h3><p>Indian Polity remains one of the highest-scoring segments with consistent questions on Fundamental Rights, Constitutional Amendments, and Panchayati Raj. Candidates should emphasize landmark Supreme Court rulings and comparative governance frameworks.</p><h3>The Role of Regular Mock Practice</h3><p>Solving questions under strict time constraints trains your mind to manage pressure and avoid negative marking traps. ParikshaMitra provides real-time explanations so you learn not just the right answer, but the underlying constitutional reason.</p>',
                'category' => 'Exam Update',
                'author' => 'ParikshaMitra Academic Council',
                'publish_date' => now()->subDays(2),
                'status' => 'PUBLISHED',
                'featured' => true,
                'display_order' => 1,
                'meta_title' => 'MPSC State Services 2026 Syllabus & MCQ Strategy | ParikshaMitra',
                'meta_description' => 'Comprehensive guide to cracking MPSC prelims with topic-wise MCQ practice and revision tips.',
            ],
            [
                'title' => 'How to Score 90+ in Maharashtra Police Bharti Written Test',
                'slug' => 'how-to-score-90-plus-in-police-bharti-written-test',
                'short_description' => 'Essential strategies for Marathi grammar, basic arithmetic, and static Maharashtra GK for Police Constable aspirants.',
                'content_html' => '<p>While physical fitness provides entry into the police recruitment shortlist, the written examination determines the final merit ranking. Scoring above 90 marks requires precision in Marathi grammar and numerical shortcuts.</p><h3>Mastering Marathi Vyakaran</h3><p>Sandhi, Samas, and Alankar form the core of the Marathi language section. Regularly practicing 50 MCQs daily allows students to spot patterns instantly.</p><h3>Mental Ability & Mathematics</h3><p>Speed is decisive. Topics such as Percentage, Ratio-Proportion, and Time & Work must be solved within 45 seconds per question.</p>',
                'category' => 'Practice Update',
                'author' => 'Shri Sunil Shinde (Ex-DySP, Advisor)',
                'publish_date' => now()->subDays(5),
                'status' => 'PUBLISHED',
                'featured' => true,
                'display_order' => 2,
                'meta_title' => 'Maharashtra Police Bharti Written Test Strategy | ParikshaMitra',
                'meta_description' => 'Tips and practice techniques to score high in the Maharashtra Police Constable written examination.',
            ],
            [
                'title' => 'CA Foundation Accounting: Avoiding Common MCQ Traps',
                'slug' => 'ca-foundation-accounting-avoiding-common-mcq-traps',
                'short_description' => 'Understanding journal conventions, depreciation adjustments, and partnership ledger balances for CA Foundation.',
                'content_html' => '<p>The CA Foundation examination tests an aspirant’s grasp of foundational accounting principles. Objective questions often present distractors based on common misconceptions regarding cash vs accrual accounting.</p><h3>True/False and Multi-Option Traps</h3><p>Always identify whether the question specifies statutory compliance or standard accounting convention before selecting your option.</p>',
                'category' => 'New Course',
                'author' => 'CA Rohan Kulkarni, FCA',
                'publish_date' => now()->subDays(7),
                'status' => 'PUBLISHED',
                'featured' => false,
                'display_order' => 3,
                'meta_title' => 'CA Foundation Accounting MCQs & Traps | ParikshaMitra',
                'meta_description' => 'Learn how to analyze accounting MCQs and score maximum marks in CA Foundation with ParikshaMitra.',
            ],
        ];

        foreach ($articles as $art) {
            NewsArticle::updateOrCreate(['slug' => $art['slug']], $art);
        }

        // 7. FAQs
        $faqs = [
            [
                'question' => 'How does the free 500 practice question quota work?',
                'answer_html' => '<p>Every registered student automatically receives a <strong>free quota of up to 500 questions</strong> per preparation field. You can select chapters, solve questions with instant answer feedback, review explanations, and bookmark key questions without any payment.</p>',
                'category' => 'Practice',
                'display_order' => 1,
                'status' => 'ACTIVE',
                'is_featured_homepage' => true,
            ],
            [
                'question' => 'Are both Marathi and English mediums supported?',
                'answer_html' => '<p>Yes! For examinations like <strong>MPSC</strong> and <strong>Police Bharti</strong>, questions, options, and explanations are authoritatively presented in both Marathi (Devanagari script) and English to ensure complete conceptual accessibility.</p>',
                'category' => 'General',
                'display_order' => 2,
                'status' => 'ACTIVE',
                'is_featured_homepage' => true,
            ],
            [
                'question' => 'What is the difference between Practice Mode and Exam Mode?',
                'answer_html' => '<p>In <strong>Practice Mode</strong>, you learn at your own pace with instant answer reveals and detailed step-by-step rationales. In <strong>Exam Mode</strong>, you face timed, realistic mock tests with question palettes, mark-for-review features, negative marking, and automated percentile rankings.</p>',
                'category' => 'Exams',
                'display_order' => 3,
                'status' => 'ACTIVE',
                'is_featured_homepage' => true,
            ],
            [
                'question' => 'How are subscriptions activated via UPI / QR Code?',
                'answer_html' => '<p>When subscribing to a premium plan, scan the official ParikshaMitra UPI QR code, transfer the exact subscription amount, and submit your <strong>12-digit UTR / Reference Number</strong> along with a screenshot receipt. Our admin verification desk activates your subscription promptly.</p>',
                'category' => 'Subscriptions',
                'display_order' => 4,
                'status' => 'ACTIVE',
                'is_featured_homepage' => true,
            ],
            [
                'question' => 'Can I prepare for Commerce subjects like CA Foundation or 12th Board?',
                'answer_html' => '<p>Yes. ParikshaMitra provides specialized question banks for <strong>11th Commerce, 12th Board, CA Foundation, CS Executive, and ICWA/CMA</strong> organized systematically by subject and chapter.</p>',
                'category' => 'General',
                'display_order' => 5,
                'status' => 'ACTIVE',
                'is_featured_homepage' => true,
            ],
            [
                'question' => 'Can I use ParikshaMitra on my smartphone?',
                'answer_html' => '<p>Absolutely! The ParikshaMitra web application is 100% responsive, optimized for mobile browsers, and engineered with touch-friendly navigation, quick-loading questions, and offline-resilient attempt caching.</p>',
                'category' => 'Account',
                'display_order' => 6,
                'status' => 'ACTIVE',
                'is_featured_homepage' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }

        // 8. Testimonials (Sample Demo Data)
        $testimonials = [
            [
                'name' => 'Akash Gaikwad',
                'designation' => 'Selected STI (Sales Tax Inspector)',
                'field' => 'MPSC State Services Aspirant',
                'content' => 'ParikshaMitra was indispensable for my prelims revision. The detailed explanations for Maharashtra History and Polity eliminated doubts that standard reference textbooks did not explain clearly. (Sample demo student review)',
                'photo' => null,
                'rating' => 5,
                'status' => 'PUBLISHED',
                'featured' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Pooja Jadhav',
                'designation' => 'Police Constable Recruitment Candidate',
                'field' => 'Maharashtra Police Bharti',
                'content' => 'The Marathi grammar and rapid mental math drills gave me the speed I needed to score 92 in the written exam. The mobile interface made daily bus commutes productive. (Sample demo student review)',
                'photo' => null,
                'rating' => 5,
                'status' => 'PUBLISHED',
                'featured' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Nikhil Joshi',
                'designation' => 'CA Foundation Aspirant (Score: 310/400)',
                'field' => 'Commerce & Professional Studies',
                'content' => 'Practicing chapter-level accounting MCQs on ParikshaMitra helped me spot journal adjustment pitfalls before the real ICAI exam. Highly recommended for commerce aspirants! (Sample demo student review)',
                'photo' => null,
                'rating' => 5,
                'status' => 'PUBLISHED',
                'featured' => true,
                'display_order' => 3,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::updateOrCreate(['name' => $t['name']], $t);
        }

        // 9. Static Pages (About Us, Privacy Policy, Terms & Conditions, Refund Policy)
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'meta_title' => 'About ParikshaMitra - Your Exam Preparation Partner',
                'meta_description' => 'Learn about ParikshaMitra, our mission to democratize quality MCQ practice for competitive examinations across Maharashtra.',
                'status' => 'PUBLISHED',
                'content_html' => '
                    <div class="space-y-8">
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">Who We Are</h2>
                            <p class="text-gray-700 leading-relaxed"><strong>ParikshaMitra</strong> ("Your Exam Preparation Partner") is an enterprise-grade digital practice and examination platform engineered specifically for students and aspirants preparing for competitive examinations. Headquartered in Maharashtra, we bridge the gap between textbook theory and examination triumph by providing high-quality, verified multiple-choice questions with thorough bilingual conceptual explanations.</p>
                        </section>
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">Our Mission</h2>
                            <p class="text-gray-700 leading-relaxed">To democratize affordable, high-yield examination preparation for every aspirant regardless of geography. We believe that regular, structured MCQ practice combined with instant analytical feedback is the single most powerful factor determining competitive rank.</p>
                        </section>
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">Our Vision</h2>
                            <p class="text-gray-700 leading-relaxed">To become India’s most trusted, dependable examination preparation ecosystem, empowering millions of aspirants to achieve their career dreams in Civil Services, Police forces, and Professional Commerce fields.</p>
                        </section>
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">What We Offer</h2>
                            <ul class="list-disc pl-5 space-y-2 text-gray-700">
                                <li><strong>Bilingual Question Bank:</strong> Authentically authored in Marathi and English with precise nuances.</li>
                                <li><strong>Dual Preparation Modes:</strong> Stress-free Practice Mode for learning, and timed Mock Exam Mode for test simulations.</li>
                                <li><strong>Generous Free Access:</strong> Up to 500 questions completely free to experience platform quality before subscribing.</li>
                                <li><strong>Affordable Premium Passes:</strong> Flexible subscriptions verified through transparent UPI and bank transfers.</li>
                            </ul>
                        </section>
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">Our Preparation Areas</h2>
                            <p class="text-gray-700 leading-relaxed">We initially focus on three vital domains: <strong>MPSC State Services & Subordinate Services</strong>, <strong>Maharashtra Police Bharti</strong>, and <strong>Commerce</strong> (including 11th & 12th State Board, CA Foundation, CS Executive, and ICWA/CMA), with generic architecture allowing continuous expansion into future examination streams.</p>
                        </section>
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">Our Approach & Future Vision</h2>
                            <p class="text-gray-700 leading-relaxed">By combining strict syllabus mapping, empirical past-year question patterns, and modern web accessibility, ParikshaMitra continuously adapts to evolving commission guidelines. As we advance, mobile applications, AI-assisted performance diagnostics, and peer-to-peer benchmarking will further elevate student success.</p>
                        </section>
                    </div>
                ',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'meta_title' => 'Privacy Policy | ParikshaMitra',
                'meta_description' => 'Read how ParikshaMitra collects, protects, and handles your personal information, mobile verification, and usage data.',
                'status' => 'PUBLISHED',
                'content_html' => '
                    <div class="space-y-6 text-gray-700 leading-relaxed">
                        <h2 class="text-xl font-bold text-gray-900">1. Introduction</h2>
                        <p>Welcome to ParikshaMitra. We respect your privacy and are committed to safeguarding personal data entrusted to us by students, educators, and administrators.</p>
                        <h2 class="text-xl font-bold text-gray-900">2. Information We Collect</h2>
                        <p>We collect information required to provide, authenticate, and personalize educational practice and examination services.</p>
                        <h2 class="text-xl font-bold text-gray-900">3. Registration Information</h2>
                        <p>During registration, we collect your full name, primary mobile number, email address, date of birth, and location details.</p>
                        <h2 class="text-xl font-bold text-gray-900">4. Mobile Number and OTP Authentication</h2>
                        <p>We utilize one-time passwords (OTP) delivered via SMS for secure, password-resilient authentication and fraud prevention.</p>
                        <h2 class="text-xl font-bold text-gray-900">5. Date of Birth and Address</h2>
                        <p>Collected to verify eligibility criteria for specific competitive examination practice modules.</p>
                        <h2 class="text-xl font-bold text-gray-900">6. Payment Information</h2>
                        <p>For manual payments, we record the chosen plan, amount, transfer timestamp, and verification reference number.</p>
                        <h2 class="text-xl font-bold text-gray-900">7. Payment Receipts</h2>
                        <p>Images uploaded as proof of bank transfer or UPI payment are stored securely and accessible only by authorized administrative auditors.</p>
                        <h2 class="text-xl font-bold text-gray-900">8. UTR / Transaction Information</h2>
                        <p>Unique Transaction Reference (UTR) numbers are matched against banking statements to authorize subscription activation.</p>
                        <h2 class="text-xl font-bold text-gray-900">9. Practice and Usage Data</h2>
                        <p>We record questions attempted, chosen options, accuracy scores, time per question, and bookmarks to calculate student progress analytics.</p>
                        <h2 class="text-xl font-bold text-gray-900">10. Device and Technical Information</h2>
                        <p>We capture browser type, operating system, IP address, and session timestamps to protect system security and enforce rate limiting.</p>
                        <h2 class="text-xl font-bold text-gray-900">11. How Information Is Used</h2>
                        <p>Data is utilized strictly to provide practice services, generate scorecards, process subscriptions, and communicate vital exam updates.</p>
                        <h2 class="text-xl font-bold text-gray-900">12. Data Sharing</h2>
                        <p>ParikshaMitra does NOT sell, rent, or trade student personal records or contact details to third-party commercial marketing entities.</p>
                        <h2 class="text-xl font-bold text-gray-900">13. Service Providers</h2>
                        <p>We partner with trusted infrastructure providers (SMS gateways, hosting services) bound by strict confidentiality covenants.</p>
                        <h2 class="text-xl font-bold text-gray-900">14. Data Security</h2>
                        <p>All sensitive information is encrypted in transit via SSL/TLS and protected with role-based access control and immutable audit logs.</p>
                        <h2 class="text-xl font-bold text-gray-900">15. Data Retention</h2>
                        <p>Account and progress records are maintained for active accounts and for legally mandated accounting reconciliation windows.</p>
                        <h2 class="text-xl font-bold text-gray-900">16. User Rights</h2>
                        <p>Users may review their profile, correct biographical information, and view past payment invoices directly from the student dashboard.</p>
                        <h2 class="text-xl font-bold text-gray-900">17. Account Deletion</h2>
                        <p>You may request account deactivation and data purging by contacting support@parikshamitra.com with your registered mobile number.</p>
                        <h2 class="text-xl font-bold text-gray-900">18. Cookies and Local Storage</h2>
                        <p>Session cookies and CSRF tokens are utilized strictly to maintain secure login states across page transitions.</p>
                        <h2 class="text-xl font-bold text-gray-900">19. Children’s Privacy</h2>
                        <p>ParikshaMitra is intended for secondary students (11th & 12th) and adult competitive exam aspirants. Minor accounts must be supervised by a parent or guardian.</p>
                        <h2 class="text-xl font-bold text-gray-900">20. Policy Changes</h2>
                        <p>Any revisions to this privacy statement will be announced via website notice and become effective upon publication.</p>
                        <h2 class="text-xl font-bold text-gray-900">21. Contact Information</h2>
                        <p>For privacy inquiries, write to: Privacy Officer, ParikshaMitra, FC Road, Pune, Maharashtra, or email support@parikshamitra.com.</p>
                    </div>
                ',
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'meta_title' => 'Terms & Conditions of Service | ParikshaMitra',
                'meta_description' => 'Read our terms of service, user responsibilities, question bank intellectual property, and subscription rules.',
                'status' => 'PUBLISHED',
                'content_html' => '
                    <div class="space-y-6 text-gray-700 leading-relaxed">
                        <h2 class="text-xl font-bold text-gray-900">1. Introduction</h2>
                        <p>These Terms and Conditions govern access to and usage of the ParikshaMitra web application and examination prep services.</p>
                        <h2 class="text-xl font-bold text-gray-900">2. Definitions</h2>
                        <p>"Platform" refers to ParikshaMitra. "Student" or "User" refers to any registered or browsing visitor. "MCQ" means multiple-choice questions.</p>
                        <h2 class="text-xl font-bold text-gray-900">3. Registration</h2>
                        <p>Users must provide accurate, current, and complete registration details including a valid Indian mobile number.</p>
                        <h2 class="text-xl font-bold text-gray-900">4. OTP Authentication</h2>
                        <p>Access credentials and OTPs are personal and non-transferable. You are responsible for all activities occurring under your account.</p>
                        <h2 class="text-xl font-bold text-gray-900">5. User Responsibilities</h2>
                        <p>Users agree not to scrape, automate, copy, or redistribute question bank contents without express written authorization.</p>
                        <h2 class="text-xl font-bold text-gray-900">6. Question Bank Content</h2>
                        <p>Questions, options, rationales, and mock exam schedules are prepared for educational guidance and do not guarantee examination selection.</p>
                        <h2 class="text-xl font-bold text-gray-900">7. Practice Services</h2>
                        <p>Practice Mode is provided on an "as is" and "as available" basis with self-paced answer evaluation.</p>
                        <h2 class="text-xl font-bold text-gray-900">8. Examination Services</h2>
                        <p>Mock exams adhere to designated countdown timers and single-attempt constraints to mirror authentic exam discipline.</p>
                        <h2 class="text-xl font-bold text-gray-900">9. Free Access Quota</h2>
                        <p>Free users are governed by a configurable question limit (default 500 questions per course). Subscribed users enjoy unlimited access.</p>
                        <h2 class="text-xl font-bold text-gray-900">10. Subscription Plans</h2>
                        <p>Plans provide time-limited, non-exclusive access to specific or all-access examination courses as detailed in the plan schedule.</p>
                        <h2 class="text-xl font-bold text-gray-900">11. Manual Payment Workflow</h2>
                        <p>Payment is accepted via bank transfer and UPI. Users must transfer the exact fee indicated on the checkout screen.</p>
                        <h2 class="text-xl font-bold text-gray-900">12. UTR and Receipt Verification</h2>
                        <p>Submission of false, duplicate, or tampered UTR numbers or receipts constitutes fraud and leads to immediate account termination.</p>
                        <h2 class="text-xl font-bold text-gray-900">13. Subscription Activation</h2>
                        <p>Subscriptions activate immediately upon verification of the transaction reference by our administration team.</p>
                        <h2 class="text-xl font-bold text-gray-900">14. Subscription Expiry</h2>
                        <p>Upon expiry of the plan duration, accounts automatically revert to the standard free tier without penalty.</p>
                        <h2 class="text-xl font-bold text-gray-900">15. Cancellation and Refund</h2>
                        <p>Subscriptions are non-refundable once activated, except in documented cases of duplicate billing as outlined in our Refund Policy.</p>
                        <h2 class="text-xl font-bold text-gray-900">16. Intellectual Property</h2>
                        <p>All trademarks, graphics, code, and curated questions are the exclusive intellectual property of ParikshaMitra.</p>
                        <h2 class="text-xl font-bold text-gray-900">17. Prohibited Activities</h2>
                        <p>Reverse engineering, DDOS attacks, account sharing, and automated data harvesting are strictly prohibited.</p>
                        <h2 class="text-xl font-bold text-gray-900">18. Account Suspension</h2>
                        <p>ParikshaMitra reserves the right to suspend or terminate accounts violating academic integrity or system security.</p>
                        <h2 class="text-xl font-bold text-gray-900">19. Content Accuracy</h2>
                        <p>While our editorial board strives for 100% accuracy, users may flag potential errata through our question feedback mechanism.</p>
                        <h2 class="text-xl font-bold text-gray-900">20. Third-Party Services</h2>
                        <p>We are not responsible for telecom SMS gateway downtimes or external banking network delays.</p>
                        <h2 class="text-xl font-bold text-gray-900">21. Limitation of Liability</h2>
                        <p>In no event shall ParikshaMitra be liable for indirect, incidental, or consequential damages resulting from platform usage.</p>
                        <h2 class="text-xl font-bold text-gray-900">22. Changes to Terms</h2>
                        <p>We reserve the right to amend these terms at any time. Continued usage following updates signifies acceptance.</p>
                        <h2 class="text-xl font-bold text-gray-900">23. Contact Information</h2>
                        <p>Queries regarding terms should be directed to support@parikshamitra.com or via our Contact Us desk.</p>
                    </div>
                ',
            ],
            [
                'title' => 'Refund Policy',
                'slug' => 'refund-policy',
                'meta_title' => 'Cancellation & Refund Policy | ParikshaMitra',
                'meta_description' => 'Guidelines on subscription activation, duplicate payments, failed transfers, and refund claims.',
                'status' => 'PUBLISHED',
                'content_html' => '
                    <div class="space-y-6 text-gray-700 leading-relaxed">
                        <h2 class="text-xl font-bold text-gray-900">1. Payment Verification Process</h2>
                        <p>Every subscription payment submitted via UPI or direct bank transfer is verified against actual bank statement credit records.</p>
                        <h2 class="text-xl font-bold text-gray-900">2. Subscription Activation Timeline</h2>
                        <p>Under normal operational hours, payments submitted with valid UTR numbers and clear screenshots are approved within 2 to 4 business hours.</p>
                        <h2 class="text-xl font-bold text-gray-900">3. Cancellation Policy</h2>
                        <p>Because ParikshaMitra provides extensive digital question bank access immediately upon approval, activated subscriptions cannot be cancelled mid-term.</p>
                        <h2 class="text-xl font-bold text-gray-900">4. Refund Eligibility</h2>
                        <p>Refunds are considered exclusively for duplicate transactions where a student was billed twice for the same plan due to a technical glitch.</p>
                        <h2 class="text-xl font-bold text-gray-900">5. Duplicate Payments</h2>
                        <p>If you accidentally transfer funds twice for a single plan, submit both UTR receipts to support@parikshamitra.com within 48 hours for immediate reversal of the extra amount.</p>
                        <h2 class="text-xl font-bold text-gray-900">6. Failed or Incomplete Payments</h2>
                        <p>If money is debited from your bank but does not credit our account, contact your issuing bank or UPI app with the bank reference number.</p>
                        <h2 class="text-xl font-bold text-gray-900">7. Rejected Payment Proofs</h2>
                        <p>Payments rejected by admins due to mismatched amounts, wrong accounts, or invalid UTRs will state the explicit reason in your Payment History tab.</p>
                        <h2 class="text-xl font-bold text-gray-900">8. UTR Verification Requirements</h2>
                        <p>A valid, 12-digit numeric UTR generated by NPCI is mandatory for Indian banking reconciliation.</p>
                        <h2 class="text-xl font-bold text-gray-900">9. Subscription Expiry and Renewal</h2>
                        <p>Subscriptions do not auto-debit. Upon completion of your plan term, access simply reverts to free quota unless manually renewed.</p>
                        <h2 class="text-xl font-bold text-gray-900">10. Contact Procedure for Claims</h2>
                        <p>To initiate a payment inquiry or duplicate refund, email support@parikshamitra.com with Subject: "Payment Inquiry - [Your Mobile Number] - [UTR Number]".</p>
                    </div>
                ',
            ],
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Clear public service caches
        PublicContentService::clearAllCache();
    }
}
