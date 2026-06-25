<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use App\Filament\Widgets\DashboardStatsWidget;
use App\Filament\Widgets\LatestArticlesWidget;
use App\Filament\Widgets\PendingArticlesWidget;
use App\Filament\Widgets\RecentFormSubmissionsWidget;
use App\Filament\Widgets\SystemInfoWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = '仪表盘';

    protected static ?string $navigationLabel = '仪表盘';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Home;

    protected static ?int $navigationSort = 1;

    public function getHeaderWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            PendingArticlesWidget::class,
            RecentFormSubmissionsWidget::class,
            LatestArticlesWidget::class,
            SystemInfoWidget::class,
        ];
    }
}
