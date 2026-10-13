<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedInteger('question_number')->nullable()->after('subcategory_id');
            $table->string('question_code')->nullable()->after('question_number');
        });

        // Backfill existing questions if any exist
        $questions = Question::orderBy('id')->get();
        $counters = [];

        foreach ($questions as $q) {
            $key = $q->course_id.'_'.$q->category_id.'_'.($q->subcategory_id ?? '0');
            if (! isset($counters[$key])) {
                $counters[$key] = 1;
            } else {
                $counters[$key]++;
            }

            $seq = $counters[$key];

            $course = Course::find($q->course_id);
            $category = Category::find($q->category_id);
            $subcategory = $q->subcategory_id ? Subcategory::find($q->subcategory_id) : null;

            $parts = [];
            if ($course) {
                $parts[] = Str::upper(Str::slug(Str::limit($course->name, 10, '')));
            }
            if ($category) {
                $parts[] = Str::upper(Str::slug(Str::limit($category->name, 10, '')));
            }
            if ($subcategory) {
                $parts[] = Str::upper(Str::slug(Str::limit($subcategory->name, 10, '')));
            }
            $parts[] = sprintf('%04d', $seq);

            $code = implode('-', array_filter($parts));

            $q->update([
                'question_number' => $seq,
                'question_code' => $code,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['question_number', 'question_code']);
        });
    }
};
