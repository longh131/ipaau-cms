<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    public ?int $filteredCategoryId = null;

    public function mount(): void
    {
        parent::mount();

        $this->filteredCategoryId = request()->integer('category_id') ?: null;
    }

    public function getTitle(): string|Htmlable
    {
        if ($this->filteredCategoryId) {
            $category = Category::query()->find($this->filteredCategoryId);

            if ($category) {
                return '文章管理 · '.$category->name;
            }
        }

        return parent::getTitle();
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    if ($this->filteredCategoryId) {
                        $data['category_id'] = $this->filteredCategoryId;
                    }

                    return $data;
                }),
        ];

        if ($this->filteredCategoryId) {
            $actions[] = Actions\Action::make('allArticles')
                ->label('查看全部文章')
                ->url(ArticleResource::getUrl('index'))
                ->color('gray');
        }

        return $actions;
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->filteredCategoryId) {
            $query->where('category_id', $this->filteredCategoryId);
        }

        return $query;
    }
}
