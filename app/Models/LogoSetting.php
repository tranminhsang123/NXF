<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LogoSetting extends Model
{
    protected $fillable = [
        'logo_path',
        'logo_title',
        'logo_subtitle',
    ];

    protected static ?string $cachedLogoUrl = null;

    public static function currentLogoUrl(): string
    {
        if (self::$cachedLogoUrl !== null) {
            return self::$cachedLogoUrl;
        }

        $defaultLogo = asset('images/logo/yamato.jpg');

        try {
            $setting = self::query()->latest('id')->first();
        } catch (\Throwable) {
            return $defaultLogo;
        }

        if (! $setting || ! $setting->logo_path) {
            return self::$cachedLogoUrl = $defaultLogo;
        }

        if (! Storage::disk('public')->exists($setting->logo_path)) {
            return self::$cachedLogoUrl = $defaultLogo;
        }

        return self::$cachedLogoUrl = '/storage/' . ltrim($setting->logo_path, '/');
    }

    public static function currentTitle(): string
    {
        return self::query()->latest('id')->value('logo_title') ?: '日本語';
    }

    public static function currentSubtitle(): string
    {
        return self::query()->latest('id')->value('logo_subtitle') ?: 'Học tiếng Nhật hiệu quả';
    }
}
