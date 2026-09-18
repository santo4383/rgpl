<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUS_NEW = 'NEW';

    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';

    public const STATUS_RESOLVED = 'RESOLVED';

    public const STATUS_CLOSED = 'CLOSED';

    public const STATUS_SPAM = 'SPAM';

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'subject',
        'message',
        'status',
        'assigned_to',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeActiveMessages($query)
    {
        return $query->where('status', '!=', self::STATUS_SPAM);
    }
}
