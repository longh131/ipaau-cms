<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Course;
use App\Models\CpeMember;
use App\Services\CpeMemberService;
use Filament\Resources\Pages\Page;

class ManageCourseRegistrations extends Page
{
    protected static string $resource = CourseResource::class;

    protected static ?string $title = '课程报名信息';

    protected string $view = 'filament.courses.registrations';

    public Course $record;

    /** @var array<string, mixed> */
    public array $groups = [];

    public function mount(Course $record, CpeMemberService $service): void
    {
        $this->record = $record;
        $grouped = $service->groupedForCourse($record);

        $this->groups = [
            'registered' => [
                'title' => '已报名',
                'attend' => CpeMember::ATTEND_REGISTERED,
                'rows' => $this->mapRows($grouped['registered'], $service),
            ],
            'present' => [
                'title' => '到场会员',
                'attend' => CpeMember::ATTEND_PRESENT,
                'rows' => $this->mapRows($grouped['present'], $service),
            ],
            'absent' => [
                'title' => '缺席会员',
                'attend' => CpeMember::ATTEND_ABSENT,
                'rows' => $this->mapRows($grouped['absent'], $service),
            ],
        ];
    }

    public function getTitle(): string
    {
        return '报名信息 · '.$this->record->title;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\CpeMember>  $records
     * @return array<int, array<string, mixed>>
     */
    private function mapRows($records, CpeMemberService $service): array
    {
        $rows = [];

        foreach ($records->values() as $index => $record) {
            $rows[] = $service->registrationRow($record, $index + 1);
        }

        return $rows;
    }
}
