<?php

namespace App\Http\Controllers\Api\V1\Content;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Faq;
use App\Models\NewsArticle;
use App\Models\Page;
use App\Models\PracticeAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\PublicContentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiContentController extends BaseApiController
{
    /**
     * Aggregated Home Dashboard endpoint for student mobile experience.
     */
    public function home(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Enrolled fields
        $userCourses = $user ? $user->courses()->where('is_active', true)->orderBy('sort_order')->get() : collect();
        $userCourseIds = $userCourses->pluck('id')->toArray();

        // 2. Continue Practice
        $latestAttempt = $user ? PracticeAttempt::where('user_id', $user->id)
            ->with(['course', 'question.category', 'question.subcategory'])
            ->latest('last_attempted_at')
            ->first() : null;

        $continuePractice = $latestAttempt ? [
            'course_id' => $latestAttempt->course_id,
            'course_name' => $latestAttempt->course?->name,
            'category_name' => $latestAttempt->question?->category?->name,
            'subcategory_name' => $latestAttempt->question?->subcategory?->name,
            'last_attempted_at' => $latestAttempt->last_attempted_at?->diffForHumans(),
        ] : null;

        // 3. Daily Streak
        $streak = $this->calculateStreak($user);

        // 4. Question of the Day
        $qotd = $this->getQuestionOfTheDay($user);

        // 5. Latest announcements (filtered active)
        $announcements = Announcement::where('status', 'PUBLISHED')
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'short_description', 'category', 'priority', 'link', 'created_at']);

        // 6. Latest news
        $news = NewsArticle::where('status', 'PUBLISHED')
            ->orderBy('publish_date', 'desc')
            ->take(5)
            ->get(['id', 'title', 'slug', 'short_description', 'category', 'author', 'featured_image', 'publish_date']);

        // 7. Overall progress snapshot
        $totalQuestions = $user ? $user->uniqueQuestionsAttemptedCount() : 0;
        $accuracy = $user ? $user->accuracyPercentage() : 0.0;

        return $this->successResponse([
            'greeting' => 'Welcome back, '.($user ? $user->name : 'Student'),
            'user' => [
                'name' => $user?->name,
                'mobile' => $user?->mobile_number,
                'has_active_subscription' => $user ? $user->hasActiveSubscription() : false,
            ],
            'streak' => $streak,
            'continue_practice' => $continuePractice,
            'enrolled_fields' => $userCourses->map(function ($c) use ($user) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'icon' => $c->icon,
                    'free_quota_remaining' => $user->freeQuotaRemaining($c->id),
                    'has_active_subscription' => $user->hasActiveSubscription($c->id),
                ];
            }),
            'progress_card' => [
                'questions_attempted' => $totalQuestions,
                'accuracy_percentage' => $accuracy,
                'active_courses_count' => $userCourses->count(),
            ],
            'question_of_the_day' => $qotd,
            'announcements' => $announcements,
            'latest_news' => $news,
        ]);
    }

    /**
     * List announcements.
     */
    public function announcements(Request $request): JsonResponse
    {
        $announcements = Announcement::where('status', 'PUBLISHED')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($announcements);
    }

    /**
     * List news articles with search and category filter.
     */
    public function news(Request $request): JsonResponse
    {
        $query = NewsArticle::where('status', 'PUBLISHED');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $term = '%'.$request->search.'%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('short_description', 'like', $term);
            });
        }

        $featured = NewsArticle::where('status', 'PUBLISHED')
            ->where('featured', true)
            ->latest('publish_date')
            ->first();

        $news = $query->orderBy('publish_date', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'featured' => $featured,
            'data' => $news->items(),
            'pagination' => [
                'current_page' => $news->currentPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'last_page' => $news->lastPage(),
                'has_more' => $news->hasMorePages(),
            ],
        ]);
    }

    /**
     * Get single news article detail by slug.
     */
    public function newsDetail(string $slug): JsonResponse
    {
        $article = NewsArticle::where('status', 'PUBLISHED')->where('slug', $slug)->firstOrFail();

        $related = NewsArticle::where('status', 'PUBLISHED')
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->take(3)
            ->get(['id', 'title', 'slug', 'featured_image', 'publish_date']);

        return $this->successResponse([
            'article' => $article,
            'related' => $related,
        ]);
    }

    /**
     * List FAQs.
     */
    public function faqs(): JsonResponse
    {
        $faqs = Faq::where('is_active', true)->orderBy('display_order')->get();

        return $this->successResponse($faqs);
    }

    /**
     * Get Question of the Day.
     */
    public function questionOfTheDay(Request $request): JsonResponse
    {
        $user = $request->user();
        $qotd = $this->getQuestionOfTheDay($user);

        return $this->successResponse($qotd);
    }

    /**
     * Submit Question of the Day.
     */
    public function submitQuestionOfTheDay(Request $request): JsonResponse
    {
        $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'selected_option_id' => ['required', 'exists:question_options,id'],
        ]);

        $user = $request->user();
        $question = Question::with('options')->findOrFail($request->question_id);
        $selected = QuestionOption::where('question_id', $question->id)->where('id', $request->selected_option_id)->firstOrFail();

        $isCorrect = (bool) $selected->is_correct;
        $correct = $question->options->firstWhere('is_correct', true);

        // Record in practice attempts so streak updates
        PracticeAttempt::updateOrCreate([
            'user_id' => $user->id,
            'question_id' => $question->id,
        ], [
            'course_id' => $question->course_id,
            'selected_option_id' => $selected->id,
            'is_correct' => $isCorrect,
            'last_attempted_at' => now(),
        ]);

        return $this->successResponse([
            'is_correct' => $isCorrect,
            'selected_option_id' => $selected->id,
            'correct_option_id' => $correct?->id,
            'correct_option_key' => $correct?->option_key,
            'explanation_text' => $question->explanation_text,
            'explanation_html' => $question->explanation_html,
        ]);
    }

    /**
     * Daily Quiz (10 questions).
     */
    public function dailyQuiz(Request $request): JsonResponse
    {
        $user = $request->user();
        $courseIds = $user ? $user->courses()->pluck('courses.id')->toArray() : [];

        $query = Question::where('status', 'PUBLISHED')->with(['options' => function ($q) {
            $q->orderBy('sort_order');
        }, 'images']);

        if (! empty($courseIds)) {
            $query->whereIn('course_id', $courseIds);
        }

        // Deterministic daily random seed based on today's date
        $seed = (int) Carbon::today()->format('Ymd');
        $questions = $query->inRandomOrder($seed)->take(10)->get();

        $formatted = $questions->map(function ($q, $idx) {
            return [
                'index' => $idx + 1,
                'id' => $q->id,
                'question_text' => $q->question_text,
                'question_html' => $q->question_html,
                'question_type' => $q->question_type ?? 'STANDARD_MCQ',
                'images' => $q->images->map(fn ($img) => ['id' => $img->id, 'file_path' => asset('storage/'.$img->file_path)]),
                'options' => $q->options->map(fn ($opt) => [
                    'id' => $opt->id,
                    'option_key' => $opt->option_key,
                    'option_text' => $opt->option_text,
                    'option_html' => $opt->option_html,
                ]),
            ];
        });

        return $this->successResponse([
            'quiz_date' => Carbon::today()->format('Y-m-d'),
            'duration_minutes' => 10,
            'total_questions' => $formatted->count(),
            'questions' => $formatted,
        ]);
    }

    /**
     * Helper to compute streak without toxic mechanics.
     */
    protected function calculateStreak($user): array
    {
        if (! $user) {
            return ['current_streak' => 0, 'longest_streak' => 0];
        }

        $dates = PracticeAttempt::where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as attempt_date')
            ->distinct()
            ->orderBy('attempt_date', 'desc')
            ->pluck('attempt_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        if (empty($dates)) {
            return ['current_streak' => 0, 'longest_streak' => 0];
        }

        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $currentStreak = 0;
        $checkDate = in_array($today, $dates) ? Carbon::today() : (in_array($yesterday, $dates) ? Carbon::yesterday() : null);

        if ($checkDate) {
            while (in_array($checkDate->toDateString(), $dates)) {
                $currentStreak++;
                $checkDate->subDay();
            }
        }

        return [
            'current_streak' => $currentStreak,
            'practiced_today' => in_array($today, $dates),
        ];
    }

    /**
     * Helper to resolve Question of the Day.
     */
    protected function getQuestionOfTheDay($user): ?array
    {
        $seed = (int) Carbon::today()->format('Ymd');
        $courseIds = $user ? $user->courses()->pluck('courses.id')->toArray() : [];

        $query = Question::where('status', 'PUBLISHED')->with(['options' => function ($q) {
            $q->orderBy('sort_order');
        }, 'images']);

        if (! empty($courseIds)) {
            $query->whereIn('course_id', $courseIds);
        }

        $q = $query->inRandomOrder($seed)->first();
        if (! $q) {
            return null;
        }

        $attempt = $user ? PracticeAttempt::where('user_id', $user->id)->where('question_id', $q->id)->first() : null;
        $hasAttempted = $attempt !== null;

        return [
            'id' => $q->id,
            'question_text' => $q->question_text,
            'question_html' => $q->question_html,
            'question_type' => $q->question_type ?? 'STANDARD_MCQ',
            'has_attempted' => $hasAttempted,
            'user_selected_option_id' => $attempt?->selected_option_id,
            'is_user_correct' => $attempt?->is_correct,
            'explanation_text' => $hasAttempted ? $q->explanation_text : null,
            'explanation_html' => $hasAttempted ? $q->explanation_html : null,
            'images' => $q->images->map(fn ($img) => ['id' => $img->id, 'file_path' => asset('storage/'.$img->file_path)]),
            'options' => $q->options->map(fn ($opt) => [
                'id' => $opt->id,
                'option_key' => $opt->option_key,
                'option_text' => $opt->option_text,
                'option_html' => $opt->option_html,
                'is_correct' => $hasAttempted ? (bool) $opt->is_correct : null,
            ]),
        ];
    }

    /**
     * Aggregated Public Homepage endpoint matching web homepage experience.
     */
    public function publicHome(Request $request, PublicContentService $contentService): JsonResponse
    {
        $data = $contentService->getHomepageData();
        $user = $request->user('sanctum');

        $topAnnouncement = $data['topAnnouncement'];
        $topAnnouncementData = $topAnnouncement ? [
            'id' => $topAnnouncement->id,
            'title' => $topAnnouncement->title,
            'short_description' => $topAnnouncement->short_description,
            'category' => $topAnnouncement->category ?? 'Notice',
            'priority' => $topAnnouncement->priority ?? 'HIGH',
            'link' => $topAnnouncement->link,
            'button_text' => $topAnnouncement->button_text ?? 'View Details',
        ] : null;

        return $this->successResponse([
            'top_announcement' => $topAnnouncementData,
            'sliders' => $data['sliders']->map(function ($s) {
                return [
                    'id' => $s->id,
                    'title' => $s->title,
                    'subtitle' => $s->subtitle ?? 'Online MCQ Practice Platform',
                    'description' => $s->description,
                    'desktop_image' => $s->desktop_image ? asset('storage/'.$s->desktop_image) : null,
                    'mobile_image' => $s->mobile_image ? asset('storage/'.$s->mobile_image) : null,
                    'button_text' => $s->button_text ?? 'Start Practicing',
                    'button_url' => $s->button_url ?? '/practice',
                    'secondary_button_text' => $s->secondary_button_text,
                    'secondary_button_url' => $s->secondary_button_url,
                ];
            }),
            'courses' => $data['courses']->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'description' => $c->description,
                    'icon' => $c->icon,
                    'questions_count' => $c->questions_count ?? 0,
                ];
            }),
            'statistics' => $data['statistics']->map(function ($stat) {
                return [
                    'id' => $stat->id,
                    'title' => $stat->title,
                    'display_value' => $stat->display_value,
                    'description' => $stat->description,
                ];
            }),
            'featured_categories' => $data['featuredCategories']->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'course_name' => $cat->course?->name,
                    'course_slug' => $cat->course?->slug,
                    'description' => $cat->description,
                    'subcategories_count' => $cat->subcategories_count ?? 4,
                ];
            }),
            'latest_news' => $data['latestNews']->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'slug' => $n->slug,
                    'short_description' => $n->short_description,
                    'category' => $n->category,
                    'author' => $n->author ?? 'Editorial Desk',
                    'featured_image' => $n->featured_image ? asset('storage/'.$n->featured_image) : null,
                    'publish_date' => $n->publish_date?->format('M d, Y'),
                ];
            }),
            'latest_announcements' => $data['latestAnnouncements']->map(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'short_description' => $a->short_description,
                    'category' => $a->category ?? 'Notice',
                    'priority' => $a->priority ?? 'MEDIUM',
                    'link' => $a->link,
                    'button_text' => $a->button_text ?? 'View Details',
                    'date' => $a->created_at?->format('M d, Y'),
                ];
            }),
            'testimonials' => $data['testimonials']->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'designation' => $t->designation,
                    'field' => $t->field,
                    'content' => $t->content,
                    'rating' => $t->rating ?? 5,
                    'avatar' => $t->avatar ? asset('storage/'.$t->avatar) : null,
                ];
            }),
            'plans' => $data['plans']->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => (float) $p->price,
                    'duration_days' => $p->duration_days,
                    'description' => $p->description,
                    'features' => [
                        'Unlimited question bank solving',
                        'Full access to mock examinations',
                        'Detailed answer rationales',
                        'Instant UPI transfer activation',
                    ],
                ];
            }),
            'faqs' => $data['faqs']->map(function ($f) {
                return [
                    'id' => $f->id,
                    'question' => $f->question,
                    'answer_html' => $f->answer_html,
                    'category' => $f->category,
                ];
            }),
            'free_limit' => $data['freeLimit'],
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'mobile_number' => $user->mobile_number,
                'has_active_subscription' => $user->hasActiveSubscription(),
            ] : null,
        ]);
    }

    /**
     * Search across courses, categories, announcements, and news.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (empty($q)) {
            return $this->successResponse([
                'query' => '',
                'courses' => [],
                'categories' => [],
                'announcements' => [],
                'news' => [],
                'total_results' => 0,
            ]);
        }

        $courses = Course::active()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->get(['id', 'name', 'slug', 'description']);

        $categories = Category::active()
            ->with('course:id,name,slug')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->get(['id', 'course_id', 'name', 'slug', 'description']);

        $announcements = Announcement::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            })
            ->get(['id', 'title', 'short_description', 'category', 'link', 'created_at']);

        $news = NewsArticle::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            })
            ->get(['id', 'title', 'slug', 'short_description', 'category', 'publish_date']);

        $total = $courses->count() + $categories->count() + $announcements->count() + $news->count();

        return $this->successResponse([
            'query' => $q,
            'courses' => $courses,
            'categories' => $categories,
            'announcements' => $announcements,
            'news' => $news,
            'total_results' => $total,
        ]);
    }

    /**
     * Submit Contact Message inquiry.
     */
    public function submitContact(Request $request): JsonResponse
    {
        if (! empty($request->input('website_hp'))) {
            ContactMessage::create([
                'name' => strip_tags((string) $request->input('name')),
                'mobile' => strip_tags((string) $request->input('mobile')),
                'email' => strip_tags((string) $request->input('email')),
                'subject' => 'SPAM BOT SUBMISSION',
                'message' => strip_tags((string) $request->input('message')),
                'status' => ContactMessage::STATUS_SPAM,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
            ]);

            return $this->successResponse(null, 'Thank you. Your message has been received.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        ContactMessage::create([
            'name' => strip_tags($validated['name']),
            'mobile' => strip_tags($validated['mobile']),
            'email' => ! empty($validated['email']) ? strip_tags($validated['email']) : null,
            'subject' => strip_tags($validated['subject']),
            'message' => strip_tags($validated['message']),
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return $this->successResponse(null, 'Thank you! Your message has been received. Our team will contact you shortly.');
    }

    /**
     * Public About Us page content.
     */
    public function about(): JsonResponse
    {
        $page = Page::published()->where('slug', 'about-us')->first();

        return $this->successResponse([
            'title' => $page?->title ?? 'About RGPL Academy',
            'subtitle' => $page?->meta_description ?? 'Your Trusted Exam Preparation Partner',
            'content_html' => $page?->content_html,
            'features' => [
                ['title' => 'Curated Question Bank', 'description' => 'Over 25,000+ syllabus-aligned questions with verified explanations.'],
                ['title' => 'Bilingual Learning', 'description' => 'Full English and Marathi Devanagari conceptual rationales.'],
                ['title' => 'Real Exam Simulation', 'description' => 'Countdown timers, negative marking, and question palettes.'],
                ['title' => 'Zero-Risk Starter', 'description' => '500 free practice questions before needing any subscription.'],
            ],
        ]);
    }
}
