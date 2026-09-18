<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $action,
        string $module,
        ?string $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): ?self {
        try {
            $effectiveUserId = $userId ?: Auth::id();

            return static::create([
                'user_id' => $effectiveUserId,
                'action' => strtoupper($action),
                'module' => strtoupper($module),
                'record_id' => $recordId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Audit log recording failed: '.$e->getMessage());

            return null;
        }
    }
}
