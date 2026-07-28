<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['sort_order'] = max(0, (int) ($data['sort_order'] ?? 0));

        return $data;
    }

    public function getTitle(): string
    {
        return '编辑课程';
    }
}
