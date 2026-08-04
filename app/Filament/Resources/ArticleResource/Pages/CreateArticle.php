<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Concerns\GeneratesArticleSlug;
use App\Filament\Resources\ArticleResource;
use App\Models\Category;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    use GeneratesArticleSlug;

    protected static string $resource = ArticleResource::class;

    public function mount(): void
    {
        parent::mount();

        $defaults = [
            'published_at' => now(),
            'is_active' => true,
        ];

        $categoryId = request()->integer('category_id');

        if ($categoryId > 0 && $this->isArticleCategory($categoryId)) {
            $defaults['category_id'] = $categoryId;
        }

        $this->form->fill($defaults);
    }

    protected function getRedirectUrl(): string
    {
        $url = $this->getResource()::getUrl('create');

        $categoryId = $this->record?->category_id;

        if ($categoryId) {
            $url .= '?category_id='.(int) $categoryId;
        }

        return $url;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = ArticleResource::normalizeArticleData($data);

        if (blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        if (! array_key_exists('is_active', $data) || $data['is_active'] === null) {
            $data['is_active'] = true;
        }

        return $data;
    }

    private function isArticleCategory(int $categoryId): bool
    {
        return Category::query()
            ->whereKey($categoryId)
            ->assignableForArticles()
            ->exists();
    }
}
