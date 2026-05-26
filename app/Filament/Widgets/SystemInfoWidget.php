<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class SystemInfoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Laravel', app()->version())
                ->description('框架版本')
                ->icon(Heroicon::CodeBracket),
            Stat::make('PHP', phpversion())
                ->description('运行环境')
                ->icon(Heroicon::CommandLine),
            Stat::make('环境', ucfirst(app()->environment()))
                ->description('当前模式')
                ->icon(Heroicon::Server)
                ->color(app()->environment('production') ? 'danger' : 'success'),
        ];
    }
}
