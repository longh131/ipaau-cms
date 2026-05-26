<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class PageCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('页面总数', Page::count())
                ->description('所有页面')
                ->descriptionIcon(Heroicon::ArrowUpRight)
                ->icon(Heroicon::Square2Stack)
                ->color('success'),
        ];
    }
}
