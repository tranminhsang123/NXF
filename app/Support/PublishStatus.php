<?php

namespace App\Support;

class PublishStatus
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';

    public static function labels(): array
    {
        return [
            self::DRAFT => 'Bản nháp',
            self::PUBLISHED => 'Đã xuất bản',
            self::ARCHIVED => 'Đã lưu trữ',
        ];
    }
}
