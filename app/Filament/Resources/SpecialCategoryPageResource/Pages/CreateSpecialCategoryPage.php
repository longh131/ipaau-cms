<?php

namespace App\Filament\Resources\SpecialCategoryPageResource\Pages;

use App\Filament\Resources\SpecialCategoryPageResource;
use App\Models\Category;
use App\Models\SpecialCategoryPage;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialCategoryPage extends CreateRecord
{
    protected static string $resource = SpecialCategoryPageResource::class;

    public function getTitle(): string
    {
        return '新增功能栏目页';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['body_html_top'] = trim((string) ($data['body_html_top'] ?? ''));
        $data['body_html_bottom'] = trim((string) ($data['body_html_bottom'] ?? ''));

        if (($data['feature_type'] ?? SpecialCategoryPage::FEATURE_COURSE_LIST) === SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP) {
            $data['certificate_title'] = trim((string) ($data['certificate_title'] ?? ''));
            $data['certificate_summary'] = trim((string) ($data['certificate_summary'] ?? ''));
            $data['course_category_ids'] = null;
        }

        if (in_array($data['feature_type'] ?? SpecialCategoryPage::FEATURE_COURSE_LIST, [
            SpecialCategoryPage::FEATURE_VIDEO_HUB,
            SpecialCategoryPage::FEATURE_CPD_RECORDS,
        ], true)) {
            $data['certificate_title'] = null;
            $data['certificate_summary'] = null;
            $data['course_category_ids'] = null;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncCategoryListTemplate($this->record);
    }

    protected function syncCategoryListTemplate(SpecialCategoryPage $record): void
    {
        $categoryId = (int) ($record->category_id ?? 0);

        if ($categoryId > 0) {
            Category::query()
                ->whereKey($categoryId)
                ->update(['list_template' => $record->listTemplate()]);
        }
    }
}
