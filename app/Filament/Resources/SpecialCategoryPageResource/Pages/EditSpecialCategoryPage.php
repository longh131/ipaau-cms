<?php

namespace App\Filament\Resources\SpecialCategoryPageResource\Pages;

use App\Filament\Resources\SpecialCategoryPageResource;
use App\Models\Category;
use App\Models\Course;
use App\Models\SpecialCategoryPage;
use Filament\Resources\Pages\EditRecord;

class EditSpecialCategoryPage extends EditRecord
{
    protected static string $resource = SpecialCategoryPageResource::class;

    public function getTitle(): string
    {
        return '编辑功能栏目页';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! filled($data['course_category_ids'] ?? null)) {
            $data['course_category_ids'] = Course::categoryIds();
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['body_html_top'] = trim((string) ($data['body_html_top'] ?? ''));
        $data['body_html_bottom'] = trim((string) ($data['body_html_bottom'] ?? ''));

        if (($data['feature_type'] ?? SpecialCategoryPage::FEATURE_COURSE_LIST) === SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP) {
            $data['certificate_title'] = trim((string) ($data['certificate_title'] ?? ''));
            $data['certificate_summary'] = trim((string) ($data['certificate_summary'] ?? ''));
            $data['course_category_ids'] = null;
        } elseif (in_array($data['feature_type'] ?? SpecialCategoryPage::FEATURE_COURSE_LIST, [
            SpecialCategoryPage::FEATURE_VIDEO_HUB,
            SpecialCategoryPage::FEATURE_CPD_RECORDS,
        ], true)) {
            $data['certificate_title'] = null;
            $data['certificate_summary'] = null;
            $data['course_category_ids'] = null;
        } else {
            $data['certificate_title'] = null;
            $data['certificate_summary'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Category::query()
            ->whereKey($this->record->category_id)
            ->update(['list_template' => $this->record->listTemplate()]);
    }
}
