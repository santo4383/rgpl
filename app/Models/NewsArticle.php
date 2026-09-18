<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NewsArticle extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_REVIEW = 'REVIEW';

    public const STATUS_PUBLISHED = 'PUBLISHED';

    public const STATUS_SCHEDULED = 'SCHEDULED';

    public const STATUS_ARCHIVED = 'ARCHIVED';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'content_html',
        'featured_image',
        'category',
        'author',
        'publish_date',
        'expiry_date',
        'status',
        'featured',
        'display_order',
        'meta_title',
        'meta_description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
        'display_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            if (empty($article->slug) && ! empty($article->title)) {
                $baseSlug = Str::slug($article->title);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $article->id ?? 0)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }
                $article->slug = $slug;
            }
        });
    }

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
                $q->whereNull('publish_date')->orWhere('publish_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', $now);
            })
            ->orderBy('display_order', 'asc')
            ->orderBy('publish_date', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->published()->where('featured', true);
    }
}
