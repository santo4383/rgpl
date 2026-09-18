<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_REVIEW = 'REVIEW';

    public const STATUS_PUBLISHED = 'PUBLISHED';

    public const STATUS_SCHEDULED = 'SCHEDULED';

    public const STATUS_EXPIRED = 'EXPIRED';

    public const STATUS_ARCHIVED = 'ARCHIVED';

    protected $fillable = [
        'title',
        'short_description',
        'content',
        'link',
        'button_text',
        'priority',
        'category',
        'start_date',
        'end_date',
        'status',
        'featured',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished($query)
    {
        $now = now();

        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->orderByRaw("CASE WHEN priority = 'URGENT' THEN 1 WHEN priority = 'HIGH' THEN 2 WHEN priority = 'MEDIUM' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->published()->where('featured', true);
    }
}
