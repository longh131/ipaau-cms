<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\FileUpload;

/**
 * 全站统一的图片上传字段配置（public 磁盘，/storage/... 访问）。
 * 首页板块及后续 CMS 图片字段请使用此类，勿用手填路径。
 */
class ImageUpload
{
    public const DISK = 'public';

    public const MAX_SIZE_KB = 5120;

    /** @var array<int, string> */
    public const ACCEPTED_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public static function make(string $name, string $directory, string $label, ?string $helperText = null): FileUpload
    {
        $field = FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk(self::DISK)
            ->directory($directory)
            ->visibility('public')
            ->imageEditor()
            ->maxSize(self::MAX_SIZE_KB)
            ->acceptedFileTypes(self::ACCEPTED_TYPES);

        if ($helperText !== null) {
            $field->helperText($helperText);
        }

        return $field;
    }
}
