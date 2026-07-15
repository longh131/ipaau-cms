<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Support\MediaUrl;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (filled($data['cover_image'] ?? null)) {
            $data['cover_image'] = MediaUrl::normalizeStoredPath($data['cover_image']);
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return ArticleResource::normalizeArticleData($data);
    }
}
