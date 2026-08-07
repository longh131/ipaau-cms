<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Course;
use App\Support\CourseSlug;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    private ?int $estimatedCourseSortOrder = null;

    private ?int $submittedSortOrder = null;

    public function mount(): void
    {
        parent::mount();

        $this->estimatedCourseSortOrder = Course::defaultSortOrderForNew();

        $this->form->fill([
            'sort_order' => $this->estimatedCourseSortOrder,
            'is_active' => true,
            'legacy_added_at' => now()->toDateString(),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->submittedSortOrder = max(0, (int) ($data['sort_order'] ?? 0));

        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $data['slug'] = CourseSlug::fromTitle((string) $data['title']);
        }

        $data['sort_order'] = max(0, (int) ($data['sort_order'] ?? 0));
        $data['cpd_credits'] = Course::normalizeCpdCredits($data['cpd_credits'] ?? null);

        if (blank($data['legacy_added_at'] ?? null)) {
            $data['legacy_added_at'] = now()->toDateString();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $shouldSyncToId = $this->submittedSortOrder === 0
            || ($this->estimatedCourseSortOrder !== null && $this->submittedSortOrder === $this->estimatedCourseSortOrder);

        if ($shouldSyncToId && (int) $this->record->sort_order !== (int) $this->record->getKey()) {
            $this->record->updateQuietly([
                'sort_order' => $this->record->getKey(),
            ]);
        }
    }

    public function getTitle(): string
    {
        return '新增课程';
    }
}
