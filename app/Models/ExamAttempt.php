<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'submitted_at',
        'time_spent_seconds',
        'status',
        'total_questions',
        'attempted_questions',
        'correct_answers',
        'incorrect_answers',
        'unanswered_questions',
        'score_obtained',
        'total_marks',
        'percentage',
        'is_passed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'time_spent_seconds' => 'integer',
        'total_questions' => 'integer',
        'attempted_questions' => 'integer',
        'correct_answers' => 'integer',
        'incorrect_answers' => 'integer',
        'unanswered_questions' => 'integer',
        'score_obtained' => 'float',
        'total_marks' => 'float',
        'percentage' => 'float',
        'is_passed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function remainingSeconds(): int
    {
        if ($this->status !== 'IN_PROGRESS') {
            return 0;
        }

        $durationSeconds = ($this->exam->duration_minutes ?? 60) * 60;
        $elapsedSeconds = (int) round(abs($this->started_at->diffInSeconds(now())));
        $remaining = $durationSeconds - $elapsedSeconds;

        return max(0, (int) $remaining);
    }

    public function isExpired(): bool
    {
        return $this->remainingSeconds() <= 0;
    }

    public function gradeAndSubmit(bool $timedOut = false): void
    {
        if ($this->status === 'SUBMITTED' || $this->status === 'TIMED_OUT') {
            return;
        }

        DB::transaction(function () use ($timedOut) {
            $exam = $this->exam;
            $examQuestions = $exam->examQuestions()->get()->keyBy('question_id');
            $answers = $this->answers()->with('question.options')->get();

            $totalQuestions = $examQuestions->count();
            $attemptedCount = 0;
            $correctCount = 0;
            $incorrectCount = 0;
            $unansweredCount = 0;
            $totalScore = 0.0;

            foreach ($answers as $answer) {
                $eq = $examQuestions->get($answer->question_id);
                $questionMarks = $eq ? (float) $eq->marks : 1.0;
                $questionNegMarks = ($eq && $exam->has_negative_marking) ? (float) $eq->negative_marks : 0.0;

                if ($answer->is_answered && $answer->selected_option_id) {
                    $attemptedCount++;
                    $selectedOption = $answer->question ? $answer->question->options->firstWhere('id', $answer->selected_option_id) : null;
                    $isCorrect = $selectedOption && $selectedOption->is_correct;

                    if ($isCorrect) {
                        $correctCount++;
                        $marksAwarded = $questionMarks;
                        $totalScore += $marksAwarded;
                        $answer->update([
                            'is_correct' => true,
                            'marks_awarded' => $marksAwarded,
                        ]);
                    } else {
                        $incorrectCount++;
                        $marksAwarded = -$questionNegMarks;
                        $totalScore += $marksAwarded;
                        $answer->update([
                            'is_correct' => false,
                            'marks_awarded' => $marksAwarded,
                        ]);
                    }
                } else {
                    $unansweredCount++;
                    $answer->update([
                        'is_correct' => null,
                        'marks_awarded' => 0.0,
                    ]);
                }
            }

            $finalScore = round(max(0.0, $totalScore), 2);
            $totalExamMarks = (float) $exam->total_marks > 0 ? (float) $exam->total_marks : (float) $examQuestions->sum('marks');
            $percentage = $totalExamMarks > 0 ? round(($finalScore / $totalExamMarks) * 100, 2) : 0.0;
            $isPassed = $percentage >= (float) $exam->pass_percentage;
            $timeSpent = (int) round(min(($exam->duration_minutes ?? 60) * 60, max(1, abs($this->started_at->diffInSeconds(now())))));

            $this->update([
                'status' => $timedOut ? 'TIMED_OUT' : 'SUBMITTED',
                'submitted_at' => now(),
                'time_spent_seconds' => $timeSpent,
                'total_questions' => (int) $totalQuestions,
                'attempted_questions' => (int) $attemptedCount,
                'correct_answers' => (int) $correctCount,
                'incorrect_answers' => (int) $incorrectCount,
                'unanswered_questions' => (int) $unansweredCount,
                'score_obtained' => $finalScore,
                'total_marks' => $totalExamMarks,
                'percentage' => $percentage,
                'is_passed' => $isPassed,
            ]);
        });
    }
}
