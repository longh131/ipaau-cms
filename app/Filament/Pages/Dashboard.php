<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = '仪表盘';

    protected static ?string $navigationLabel = '仪表盘';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Home;
}