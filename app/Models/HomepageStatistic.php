<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageStatistic extends Model
{
    protected $fillable = [
        'title',
        'value',
        'stat_key',
        'icon',
        'description',
        'display_order',
        'status',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE')->orderBy('display_order', 'asc');
    }

    public function getDisplayValueAttribute(): string
    {
        if (! empty($this->value)) {
            return $this->value;
        }

        return match ($this->stat_key) {
            'total_questions' => number_format(Question::count()).'+',
            'total_students' => number_format(User::count()).'+',
            'free_questions' => (Setting::get('default_free_limit', 500)).'+',
            'total_courses' => (string) Course::where('is_active', true)->count(),
            'mock_exams' => (string) Exam::count(),
            default => '100+',
        };
    }
}
