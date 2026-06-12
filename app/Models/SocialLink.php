<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SocialLink extends Model
{
    protected $fillable = [
        'platform',
        'label',
        'url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function platformLabels(): array
    {
        return [
            'facebook' => 'Facebook',
            'twitter' => 'Twitter / X',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'linkedin' => 'LinkedIn',
            'zalo' => 'Zalo',
            'website' => 'Website',
        ];
    }

    public static function activeOrdered(): Collection
    {
        return self::query()
            ->active()
            ->ordered()
            ->get();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function hoverClasses(): string
    {
        return match ($this->platform) {
            'facebook' => 'hover:from-blue-600 hover:to-blue-700',
            'twitter' => 'hover:from-sky-500 hover:to-sky-600',
            'instagram' => 'hover:from-pink-600 hover:to-purple-600',
            'youtube' => 'hover:from-red-600 hover:to-red-700',
            'tiktok' => 'hover:from-slate-900 hover:to-pink-600',
            'linkedin' => 'hover:from-blue-700 hover:to-blue-800',
            'zalo' => 'hover:from-blue-500 hover:to-cyan-500',
            default => 'hover:from-slate-600 hover:to-slate-800',
        };
    }

    public function isExternalUrl(): bool
    {
        $url = trim((string) $this->url);

        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
    }
}
