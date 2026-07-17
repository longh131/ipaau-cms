<?php

namespace App\Filament\Resources\IpaMemberResource\Pages;

use App\Filament\Resources\IpaMemberResource;
use App\Support\MemberImporter;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListIpaMembers extends ListRecords
{
    protected static string $resource = IpaMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('statistics')
                ->label('会员统计')
                ->icon('heroicon-o-chart-bar')
                ->url(IpaMemberResource::getUrl('statistics')),
            Actions\Action::make('importMembers')
                ->label('批量导入（覆盖）')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form([
                    FileUpload::make('file')
                        ->label('会员全数据.xlsx')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->required()
                        ->storeFiles(false),
                ])
                ->modalDescription('上传后将删除现有全部会员数据，并按 Excel 重新导入。持证会员编号为空的行将被跳过。')
                ->action(function (array $data, MemberImporter $importer): void {
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
                        ->body("已导入 {$result['imported']} 条，跳过 {$result['skipped']} 条。")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('新增会员'),
        ];
    }
}
