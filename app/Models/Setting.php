<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $setting) {
            Cache::forget("setting_{$setting->key}");
        });

        static::deleted(function (self $setting) {
            Cache::forget("setting_{$setting->key}");
        });
    }

    public static function get(string $key, $default = null)
    {
        try {
            $data = Cache::remember("setting_{$key}", 3600, function () use ($key) {
                $record = static::where('key', $key)->first();
                if (! $record) {
                    return null;
                }

                return [
                    'value' => $record->value,
                    'type' => $record->type,
                ];
            });

            // If cache returned an incomplete object or legacy invalid payload
            if ($data instanceof \__PHP_Incomplete_Class || (! is_array($data) && ! ($data instanceof self) && $data !== null)) {
                Cache::forget("setting_{$key}");
                $record = static::where('key', $key)->first();
                $data = $record ? [
                    'value' => $record->value,
                    'type' => $record->type,
                ] : null;
            }
        } catch (\Throwable) {
            // If cache read fails, fallback to direct query safely
            $record = static::where('key', $key)->first();
            $data = $record ? [
                'value' => $record->value,
                'type' => $record->type,
            ] : null;
        }

        if (! $data) {
            return $default;
        }

        if ($data instanceof self) {
            $value = $data->value;
            $type = $data->type;
        } elseif (is_array($data) && array_key_exists('value', $data)) {
            $value = $data['value'];
            $type = $data['type'] ?? 'string';
        } else {
            return $default;
        }

        if ($value === null) {
            return $default;
        }

        return match ($type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => is_array($value) ? $value : json_decode((string) $value, true),
            default => $value,
        };
    }

    public static function set(string $key, $value, string $group = 'general', string $type = 'string'): self
    {
        $stringValue = is_array($value) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'group' => $group,
                'type' => $type,
            ]
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }
}
