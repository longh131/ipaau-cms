<?php

namespace App\Filament\Concerns;

use App\Models\Article;
use App\Support\ArticleSlug;

trait GeneratesArticleSlug
{
    public function generateArticleSlugFromTitle(): void
    {
        $title = trim((string) ($this->data['title'] ?? ''));
        $slug = trim((string) ($this->data['slug'] ?? ''));

        if ($title === '' || $slug !== '') {
            return;
        }

        $this->data['slug'] = ArticleSlug::fromTitle($title, $this->currentArticleIdForSlug());
    }

    protected function currentArticleIdForSlug(): ?int
    {
        if (! method_exists($this, 'getRecord')) {
            return null;
        }

        $record = $this->getRecord();

        if (! $record instanceof Article) {
            return null;
        }

        $id = $record->getKey();

        return is_numeric($id) ? (int) $id : null;
    }
}
