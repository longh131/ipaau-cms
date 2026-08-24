<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Course;
use App\Models\CpeMember;
use App\Services\CpeMemberService;
use App\Support\CpeMemberImporter;
use App\Support\CourseImporter;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->importCoursesAction(),
            $this->importCpeMembersAction(),
            $this->bulkAddRegistrationsAction(),
            $this->bulkUpdateRegistrationsAction(),
            Actions\CreateAction::make()
                ->label('新增课程'),
        ];
    }

    private function importCoursesAction(): Actions\Action
    {
        return Actions\Action::make('importCourses')
            ->label('批量导入（覆盖）')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->form([
                FileUpload::make('file')
                    ->label('IPA_book.xlsx')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ])
                    ->required()
                    ->storeFiles(false),
            ])
            ->modalDescription('上传后将删除现有全部课程，并按 Excel 重新导入。标题不含【】的行将被跳过；含【】的按规则自动归入四个课程分类。')
            ->action(function (array $data, CourseImporter $importer): void {
                $path = $this->resolveUploadedPath($data['file'] ?? null);

                if ($path === null) {
                    Notification::make()->title('文件无效')->danger()->send();

                    return;
                }

                try {
                    $result = $importer->import($path);
                } catch (\Throwable $exception) {
                    Notification::make()->title('导入失败')->body($exception->getMessage())->danger()->send();

                    return;
                }

                Notification::make()
                    ->title('导入完成')
                    ->body("已导入 {$result['imported']} 条；跳过无【】 {$result['skipped_no_bracket']} 条，空标题 {$result['skipped_empty']} 条。")
                    ->success()
                    ->send();
            });
    }

    private function importCpeMembersAction(): Actions\Action
    {
        return Actions\Action::make('importCpeMembers')
            ->label('报名批量导入（覆盖）')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->form([
                FileUpload::make('file')
                    ->label('cpe_member.xlsx')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ])
                    ->required()
                    ->storeFiles(false),
            ])
            ->modalDescription('上传后将删除现有全部 cpe_members 记录，并按 Excel 重新导入。活动 ID 会通过 courses.legacy_id 自动匹配 course_id。')
            ->action(function (array $data, CpeMemberImporter $importer): void {
                $path = $this->resolveUploadedPath($data['file'] ?? null);

                if ($path === null) {
                    Notification::make()->title('文件无效')->danger()->send();

                    return;
                }

                try {
                    $result = $importer->import($path, truncate: true);
                } catch (\Throwable $exception) {
                    Notification::make()->title('导入失败')->body($exception->getMessage())->danger()->send();

                    return;
                }

                Notification::make()
                    ->title('报名导入完成')
                    ->body("已导入 {$result['imported']} 条；跳过空行 {$result['skipped_empty']} 条；未匹配课程 {$result['unresolved_courses']} 条。")
                    ->success()
                    ->send();
            });
    }

    private function bulkAddRegistrationsAction(): Actions\Action
    {
        return Actions\Action::make('bulkAddRegistrations')
            ->label('报名批量添加')
            ->icon('heroicon-o-user-plus')
            ->form($this->registrationBulkForm(CpeMember::ATTEND_REGISTERED))
            ->modalDescription('为指定课程追加会员报名记录；已存在的会员号不会重复添加。')
            ->action(function (array $data, CpeMemberService $service): void {
                try {
                    $result = $service->bulkAdd(
                        (int) $data['course_id'],
                        (string) $data['member_numbers'],
                        (int) $data['attend'],
                    );
                } catch (\Throwable $exception) {
                    Notification::make()->title('添加失败')->body($exception->getMessage())->danger()->send();

                    return;
                }

                Notification::make()
                    ->title('添加完成')
                    ->body("新增 {$result['created']} 条，已存在跳过 {$result['updated']} 条。")
                    ->success()
                    ->send();
            });
    }

    private function bulkUpdateRegistrationsAction(): Actions\Action
    {
        return Actions\Action::make('bulkUpdateRegistrations')
            ->label('报名批量修改')
            ->icon('heroicon-o-pencil-square')
            ->color('gray')
            ->form($this->registrationBulkForm(CpeMember::ATTEND_PRESENT))
            ->modalDescription('批量修改指定课程下已有会员记录的 attend 状态。')
            ->action(function (array $data, CpeMemberService $service): void {
                try {
                    $result = $service->bulkUpdate(
                        (int) $data['course_id'],
                        (string) $data['member_numbers'],
                        (int) $data['attend'],
                    );
                } catch (\Throwable $exception) {
                    Notification::make()->title('修改失败')->body($exception->getMessage())->danger()->send();

                    return;
                }

                Notification::make()
                    ->title('修改完成')
                    ->body("已更新 {$result['updated']} 条，未找到 {$result['missing']} 条。")
                    ->success()
                    ->send();
            });
    }

    /**
     * @return array<int, Select|Textarea>
     */
    private function registrationBulkForm(int $defaultAttend): array
    {
        return [
            Select::make('course_id')
                ->label('课程')
                ->options(fn (): array => Course::query()
                    ->orderByDesc('starts_at')
                    ->orderByDesc('id')
                    ->get()
                    ->mapWithKeys(fn (Course $course): array => [
                        $course->getKey() => '#'.$course->getKey().' · '.$course->title,
                    ])
                    ->all())
                ->searchable()
                ->required(),
            Textarea::make('member_numbers')
                ->label('会员号')
                ->rows(6)
                ->placeholder('369105, 369103, 367215')
                ->helperText('多个会员号可用逗号、空格或换行分隔。')
                ->required(),
            Select::make('attend')
                ->label('出席状态')
                ->options(CpeMember::ATTEND_OPTIONS)
                ->default($defaultAttend)
                ->required(),
        ];
    }

    private function resolveUploadedPath(mixed $uploaded): ?string
    {
        if (is_array($uploaded)) {
            $uploaded = reset($uploaded);
        }

        if (is_string($uploaded)) {
            return is_file($uploaded) ? $uploaded : null;
        }

        if (is_object($uploaded) && method_exists($uploaded, 'getRealPath')) {
            $path = $uploaded->getRealPath();

            return is_string($path) && is_file($path) ? $path : null;
        }

        return null;
    }
}
