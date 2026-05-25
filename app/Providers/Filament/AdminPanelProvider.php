<?php

namespace App\Providers\Filament;

use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\MemberResource;
use App\Filament\Resources\PageResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Icons\IconProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources($this->getResources())
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function getResources(): array
    {
        $defaultTypes = ['article', 'page', 'link', 'member'];
        
        if (!Schema::hasTable('settings')) {
            $enabledTypes = $defaultTypes;
        } else {
            $enabledTypes = Setting::get('enabled_content_types', $defaultTypes);
        }

        $resources = collect($enabledTypes)
            ->map(fn ($type) => match ($type) {
                'article' => ArticleResource::class,
                'page' => PageResource::class,
                'product' => \App\Filament\Resources\ProductResource::class,
                'case' => \App\Filament\Resources\CaseResource::class,
                'gallery' => \App\Filament\Resources\GalleryResource::class,
                'event' => \App\Filament\Resources\EventResource::class,
                'member' => MemberResource::class,
                'download' => \App\Filament\Resources\DownloadResource::class,
                'faq' => \App\Filament\Resources\FaqResource::class,
                'form' => \App\Filament\Resources\FormResource::class,
                default => null,
            })
            ->filter()
            ->toArray();

        array_unshift($resources, CategoryResource::class);

        return $resources;
    }
}