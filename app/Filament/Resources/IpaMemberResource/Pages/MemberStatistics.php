<?php

namespace App\Filament\Resources\IpaMemberResource\Pages;

use App\Filament\Resources\IpaMemberResource;
use App\Services\MemberStatisticsService;
use Filament\Resources\Pages\Page;

class MemberStatistics extends Page
{
    protected static string $resource = IpaMemberResource::class;

    protected static ?string $title = '会员统计';

    protected static ?string $navigationLabel = '会员统计';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.ipa-member.statistics';

    public array $stats = [];

    public function mount(MemberStatisticsService $statisticsService): void
    {
        $this->stats = $statisticsService->summary();
    }
}
