<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class ArticleCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('文章总数', Article::count())
                ->description('所有文章')
                ->descriptionIcon(Heroicon::ArrowUpRight)
                ->icon(Heroicon::DocumentText)
                ->color('primary'),
        ];
    }
}
