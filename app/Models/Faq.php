<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer_html',
        'category',
        'display_order',
        'status',
        'is_featured_homepage',
    ];

    protected $casts = [
        'is_featured_homepage' => 'boolean',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE')->orderBy('display_order', 'asc');
    }

    public function scopeHomepage($query)
    {
        return $query->active()->where('is_featured_homepage', true);
    }
}
