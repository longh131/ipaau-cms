<?php

namespace App\Providers;

use App\Filament\RichEditor\Plugins\InlineStylePlugin;
use App\Services\MenuService;
use App\Services\SiteSettingsService;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MenuService::class);
        $this->app->singleton(SiteSettingsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make(
                'rich-content-plugins/inline-style',
                public_path('js/filament/rich-content-plugins/inline-style.js'),
            )->loadedOnRequest(),
        ]);

        RichEditor::configureUsing(function (RichEditor $editor): void {
            $editor
                ->plugins([
                    InlineStylePlugin::make(),
                ])
                ->enableToolbarButtons(['source-ai']);
        });

        View::composer(['layouts.app', 'partials.header.site-header'], function ($view) {
            if (! array_key_exists('menuItems', $view->getData())) {
                $view->with('menuItems', app(MenuService::class)->getHeaderMenuItems());
            }
        });

        View::composer(['partials.footer.footer-main', 'layouts.app'], function ($view) {
            $siteSettings = app(SiteSettingsService::class);
            $menuService = app(MenuService::class);

            if (! array_key_exists('footerDisclaimer', $view->getData())) {
                $view->with('footerDisclaimer', $siteSettings->getFooterDisclaimer());
            }

            if (! array_key_exists('footerCopyright', $view->getData())) {
                $view->with('footerCopyright', $siteSettings->getFooterCopyright());
            }

            if (! array_key_exists('footerSocialLinks', $view->getData())) {
                $view->with('footerSocialLinks', $siteSettings->getFooterSocialLinks());
            }

            if (! array_key_exists('footerMenuItems', $view->getData())) {
                $view->with('footerMenuItems', $menuService->getFooterMenuItems());
            }
        });
    }
}
