<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug) && ! empty($category->name)) {
                $category->slug = Str::slug($category->name);
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
