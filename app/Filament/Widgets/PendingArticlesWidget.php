<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\FormSubmission;
use App\Models\Subscriber;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingArticlesWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 6;

    protected function getStats(): array
    {
        $pendingCount = Article::whereNull('published_at')->where('is_active', true)->count();
        $recentForms = FormSubmission::where('created_at', '>=', now()->subDays(7))->count();
        $subscriberCount = Subscriber::count();

        return [
            Stat::make('待发布文章', $pendingCount)
                ->description('已启用但未发布')
                ->descriptionIcon(Heroicon::Clock)
                ->icon(Heroicon::DocumentText)
                ->color($pendingCount > 0 ? 'warning' : 'success'),
            Stat::make('近 7 日表单', $recentForms)
                ->description('待处理提交')
                ->descriptionIcon(Heroicon::Inbox)
                ->icon(Heroicon::InboxArrowDown)
                ->color($recentForms > 0 ? 'info' : 'gray'),
            Stat::make('邮件订阅', $subscriberCount)
                ->description('累计订阅用户')
                ->descriptionIcon(Heroicon::Envelope)
                ->icon(Heroicon::Users)
                ->color('primary'),
        ];
    }
}
