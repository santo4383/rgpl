<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'category_id',
        'title',
        'slug',
        'description',
        'instructions',
        'duration_minutes',
        'total_marks',
        'pass_percentage',
        'has_negative_marking',
        'default_negative_marks',
        'is_published',
        'is_premium',
        'created_by',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'total_marks' => 'float',
        'pass_percentage' => 'float',
        'has_negative_marking' => 'boolean',
        'default_negative_marks' => 'float',
        'is_published' => 'boolean',
        'is_premium' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exam) {
            if (empty($exam->slug)) {
                $exam->slug = Str::slug($exam->title).'-'.Str::random(5);
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('sort_order');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
            ->withPivot('id', 'marks', 'negative_marks', 'sort_order')
            ->withTimestamps()
            ->orderBy('exam_questions.sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function assignedMarksTotal(): float
    {
        return (float) $this->examQuestions()->sum('marks');
    }

    public function remainingMarks(): float
    {
        return max(0.0, (float) $this->total_marks - $this->assignedMarksTotal());
    }

    public function canAddQuestionWithMarks(float $marks): bool
    {
        return ($this->assignedMarksTotal() + $marks) <= ((float) $this->total_marks + 0.001);
    }

    public function userAttempt($userId)
    {
        return $this->attempts()->where('user_id', $userId)->latest()->first();
    }

    public function hasUserCompleted($userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereIn('status', ['SUBMITTED', 'TIMED_OUT'])
            ->exists();
    }
}
