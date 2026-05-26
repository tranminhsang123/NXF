<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SecuritySetting extends Model
{
    protected $table = 'security_settings';

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = 'security_setting:' . $key;
        return Cache::remember($cacheKey, 300, function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            return $row ? $row->value : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
        );
        Cache::forget('security_setting:' . $key);
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $v = static::get($key, $default ? '1' : '0');
        return $v === '1' || $v === true;
    }

    public static function getInt(string $key, int $default = 0): int
    {
        return (int) static::get($key, (string) $default);
    }
}
