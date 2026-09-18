<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticeAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'question_id',
        'selected_option_id',
        'is_correct',
        'attempt_count',
        'last_attempted_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'last_attempted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
