<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class CategoryCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('栏目总数', Category::count())
                ->description('所有栏目')
                ->descriptionIcon(Heroicon::ArrowUpRight)
                ->icon(Heroicon::FolderOpen)
                ->color('secondary'),
        ];
    }
}
