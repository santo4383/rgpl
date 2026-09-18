<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_PUBLISHED = 'PUBLISHED';

    public const STATUS_ARCHIVED = 'ARCHIVED';

    protected $fillable = [
        'name',
        'designation',
        'field',
        'content',
        'photo',
        'rating',
        'status',
        'featured',
        'display_order',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'rating' => 'integer',
        'display_order' => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->whereIn('status', [self::STATUS_PUBLISHED, self::STATUS_APPROVED])
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->published()->where('featured', true);
    }
}
