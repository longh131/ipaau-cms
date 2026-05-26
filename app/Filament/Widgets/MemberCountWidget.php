<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class MemberCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('会员总数', Member::count())
                ->description('所有会员')
                ->descriptionIcon(Heroicon::ArrowUpRight)
                ->icon(Heroicon::Users)
                ->color('warning'),
        ];
    }
}
