<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'is_featured',
        'sort_order',
        'free_question_limit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($course) {
            if (empty($course->slug) && ! empty($course->name)) {
                $course->slug = Str::slug($course->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_active', true)->where('is_featured', true)->orderBy('sort_order', 'asc');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }

    public function subscriptionPlans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'course_subscription_plan');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
