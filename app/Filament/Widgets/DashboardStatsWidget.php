<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class DashboardStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('文章总数', Article::count())
                ->description('所有文章')
                ->descriptionIcon(Heroicon::ArrowUpRight)
                ->icon(Heroicon::DocumentText)
                ->color('primary'),
            Stat::make('栏目总数', Category::count())
                ->description('所有栏目')
                ->descriptionIcon(Heroicon::ArrowUpRight)
                ->icon(Heroicon::FolderOpen)
                ->color('secondary'),
            Stat::make('页面总数', Page::count())
                ->description('所有页面')
                ->descriptionIcon(Heroicon::ArrowUpRight)
                ->icon(Heroicon::Square2Stack)
                ->color('success'),
            Stat::make('会员总数', Member::count())
                ->description('所有会员')
                ->descriptionIcon(Heroicon::ArrowUpRight)
                ->icon(Heroicon::Users)
                ->color('warning'),
        ];
    }
}
