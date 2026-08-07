<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Concerns\GeneratesArticleSlug;
use App\Filament\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Category;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    use GeneratesArticleSlug;

    protected static string $resource = ArticleResource::class;

    private ?int $estimatedArticleSortOrder = null;

    private ?int $submittedSortOrder = null;

    public function mount(): void
    {
        parent::mount();

        $this->estimatedArticleSortOrder = Article::defaultSortOrderForNew();

        $defaults = [
            'published_at' => now(),
            'is_active' => true,
            'sort_order' => $this->estimatedArticleSortOrder,
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
        $this->submittedSortOrder = max(0, (int) ($data['sort_order'] ?? 0));

        $data = ArticleResource::normalizeArticleData($data);

        if (blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        if (! array_key_exists('is_active', $data) || $data['is_active'] === null) {
            $data['is_active'] = true;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $shouldSyncToId = $this->submittedSortOrder === 0
            || ($this->estimatedArticleSortOrder !== null && $this->submittedSortOrder === $this->estimatedArticleSortOrder);

        if ($shouldSyncToId && (int) $this->record->sort_order !== (int) $this->record->getKey()) {
            $this->record->updateQuietly([
                'sort_order' => $this->record->getKey(),
            ]);
        }
    }

    private function isArticleCategory(int $categoryId): bool
    {
        return Category::query()
            ->whereKey($categoryId)
            ->assignableForArticles()
            ->exists();
    }
}
