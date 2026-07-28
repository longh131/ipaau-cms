<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Support\CourseSlug;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $data['slug'] = CourseSlug::fromTitle((string) $data['title']);
        }

        $data['sort_order'] = max(0, (int) ($data['sort_order'] ?? 0));

        if (blank($data['legacy_added_at'] ?? null)) {
            $data['legacy_added_at'] = now()->toDateString();
        }

        return $data;
    }

    public function getTitle(): string
    {
        return '新增课程';
    }
}
