<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSlider extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'desktop_image',
        'mobile_image',
        'button_text',
        'button_url',
        'secondary_button_text',
        'secondary_button_url',
        'display_order',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->orderBy('display_order', 'asc');
    }
}
