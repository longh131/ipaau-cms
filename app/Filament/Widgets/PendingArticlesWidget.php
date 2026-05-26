<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class PendingArticlesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $pendingCount = Article::whereNull('published_at')->count();
        
        return [
            Stat::make('待发布文章', $pendingCount)
                ->description('需要审核发布')
                ->descriptionIcon(Heroicon::Clock)
                ->icon(Heroicon::Clock)
                ->color($pendingCount > 0 ? 'danger' : 'success'),
        ];
    }
}
