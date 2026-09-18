<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'selected_option_id',
        'is_marked_for_review',
        'is_answered',
        'is_correct',
        'marks_awarded',
        'time_spent_seconds',
    ];

    protected $casts = [
        'is_marked_for_review' => 'boolean',
        'is_answered' => 'boolean',
        'is_correct' => 'boolean',
        'marks_awarded' => 'float',
        'time_spent_seconds' => 'integer',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
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
