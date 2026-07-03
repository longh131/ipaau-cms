<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Category;
use App\Models\Page;
use App\Support\PageTemplate\PageBodyBlocks;
use App\Support\PageTemplate\PageTemplateRegistry;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return static::normalizePageData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizePageData(array $data): array
    {
        $category = Category::query()->find($data['category_id'] ?? null);

        if (! $category) {
            throw ValidationException::withMessages([
                'category_id' => '请选择有效的单页栏目。',
            ]);
        }

        if ($category->type !== 'page') {
            throw ValidationException::withMessages([
                'category_id' => '所选栏目类型必须为「单页」。',
            ]);
        }

        $data['slug'] = $category->slug;

        if (blank($data['title'] ?? null)) {
            $data['title'] = $category->name;
        }

        $template = (string) ($data['template'] ?? Page::TEMPLATE_DEFAULT);
        $data['data'] = PageTemplateRegistry::forStorage(is_array($data['data'] ?? null) ? $data['data'] : [], $template);
        $data['content'] = PageBodyBlocks::legacyContentSnapshot($data['data']['body_blocks'] ?? null);

        $bodyBlocks = $data['data']['body_blocks'] ?? [];

        if ($template === Page::TEMPLATE_DEFAULT && ! PageBodyBlocks::hasContent($bodyBlocks)) {
            throw ValidationException::withMessages([
                'data.body_blocks' => '请至少添加一个正文区块。',
            ]);
        }

        return $data;
    }
}
