<?php

namespace App\Providers;

use App\Filament\RichEditor\Plugins\InlineStylePlugin;
use App\Services\MenuService;
use App\Services\PageComponentService;
use App\Services\SiteSettingsService;
use App\Support\RichContent;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Icons\Heroicon;
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
        $this->app->singleton(PageComponentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RichEditor::configureUsing(function (RichEditor $editor): void {
            $editor
                ->plugins([
                    InlineStylePlugin::make(),
                ])
                ->toolbarButtons(RichContent::pageToolbar())
                ->resizableImages(true)
                ->fileAttachmentsDisk(RichContent::fileAttachmentsDisk())
                ->fileAttachmentsDirectory(RichContent::fileAttachmentsDirectory())
                ->fileAttachmentsVisibility(RichContent::fileAttachmentsVisibility())
                ->tools([
                    RichEditorTool::make('h4')
                        ->label('H4')
                        ->jsHandler('(() => { const editor = $getEditor(); if (!editor) { return } if (editor.isActive(\'heading\', { level: 4 })) { editor.chain().focus().toggleHeading({ level: 4 }).run(); return } if (!editor.chain().focus().toggleHeading({ level: 4 }).run()) { return } const { $from } = editor.state.selection; const markType = editor.state.schema.marks.genericSpan; if (!markType) { return } for (let depth = $from.depth; depth > 0; depth--) { const node = $from.node(depth); if (node.type.name !== \'heading\' || node.attrs.level !== 4) { continue } editor.chain().focus().setTextSelection({ from: $from.start(depth), to: $from.end(depth) }).setMark(\'genericSpan\', { class: \'text-warm-plum\' }).run(); return } })()')
                        ->activeKey('heading')
                        ->activeOptions(['level' => 4])
                        ->icon('fi-o-h4')
                        ->iconAlias('forms:components.rich-editor.toolbar.h4'),
                    RichEditorTool::make('attachFiles')
                        ->label('插入图片')
                        ->action(arguments: '{ alt: $getEditor().getAttributes(\'image\')?.alt, id: $getEditor().getAttributes(\'image\')?.id, src: $getEditor().getAttributes(\'image\')?.src }')
                        ->activeKey('image')
                        ->icon(Heroicon::Photo)
                        ->iconAlias('forms:components.rich-editor.toolbar.attach-files'),
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
