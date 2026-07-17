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

        $categoryId = request()->integer('category_id');

        if ($categoryId <= 0 || ! $this->isArticleCategory($categoryId)) {
            return;
        }

        $this->form->fill([
            'category_id' => $categoryId,
        ]);
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
        return ArticleResource::normalizeArticleData($data);
    }

    private function isArticleCategory(int $categoryId): bool
    {
        return Category::query()
            ->whereKey($categoryId)
            ->where('type', 'article')
            ->exists();
    }
}
