<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Support\CourseImporter;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importCourses')
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
                    $uploaded = $data['file'];

                    if (is_array($uploaded)) {
                        $uploaded = reset($uploaded);
                    }

                    $path = null;

                    if (is_string($uploaded)) {
                        $path = $uploaded;
                    } elseif (is_object($uploaded) && method_exists($uploaded, 'getRealPath')) {
                        $path = $uploaded->getRealPath();
                    }

                    if ($path === null || ! is_file($path)) {
                        Notification::make()
                            ->title('文件无效')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $result = $importer->import($path);
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('导入失败')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('导入完成')
                        ->body("已导入 {$result['imported']} 条；跳过无【】 {$result['skipped_no_bracket']} 条，空标题 {$result['skipped_empty']} 条。")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('新增课程'),
        ];
    }
}
