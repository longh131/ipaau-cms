<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = '系统设置';

    protected static ?string $navigationLabel = '系统设置';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cog;
    
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.settings';

    public ?string $site_name = '';
    public ?string $site_description = '';
    public ?string $site_logo = '';
    public ?string $contact_email = '';
    public ?string $contact_phone = '';
    public ?string $contact_address = '';
    public ?string $social_facebook = '';
    public ?string $social_twitter = '';
    public ?string $social_instagram = '';
    public ?string $social_linkedin = '';
    public ?string $seo_title = '';
    public ?string $seo_description = '';
    public ?string $seo_keywords = '';
    public ?bool $maintenance_mode = false;

    public function mount(): void
    {
        $this->site_name = Setting::get('site_name', '');
        $this->site_description = Setting::get('site_description', '');
        $this->site_logo = Setting::get('site_logo', '');
        $this->contact_email = Setting::get('contact_email', '');
        $this->contact_phone = Setting::get('contact_phone', '');
        $this->contact_address = Setting::get('contact_address', '');
        $this->social_facebook = Setting::get('social_facebook', '');
        $this->social_twitter = Setting::get('social_twitter', '');
        $this->social_instagram = Setting::get('social_instagram', '');
        $this->social_linkedin = Setting::get('social_linkedin', '');
        $this->seo_title = Setting::get('seo_title', '');
        $this->seo_description = Setting::get('seo_description', '');
        $this->seo_keywords = Setting::get('seo_keywords', '');
        $this->maintenance_mode = Setting::get('maintenance_mode', false);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('site_name')
                ->label('网站名称')
                ->required(),
            Forms\Components\TextInput::make('site_logo')
                ->label('网站 Logo')
                ->placeholder('Logo 路径或 URL'),
            Forms\Components\Textarea::make('site_description')
                ->label('网站描述')
                ->rows(3),
            Forms\Components\TextInput::make('contact_email')
                ->label('联系邮箱')
                ->email(),
            Forms\Components\TextInput::make('contact_phone')
                ->label('联系电话'),
            Forms\Components\Textarea::make('contact_address')
                ->label('联系地址')
                ->rows(2),
            Forms\Components\TextInput::make('social_facebook')
                ->label('Facebook')
                ->url(),
            Forms\Components\TextInput::make('social_twitter')
                ->label('Twitter')
                ->url(),
            Forms\Components\TextInput::make('social_instagram')
                ->label('Instagram')
                ->url(),
            Forms\Components\TextInput::make('social_linkedin')
                ->label('LinkedIn')
                ->url(),
            Forms\Components\TextInput::make('seo_title')
                ->label('SEO 标题'),
            Forms\Components\Textarea::make('seo_description')
                ->label('SEO 描述')
                ->rows(2),
            Forms\Components\TextInput::make('seo_keywords')
                ->label('SEO 关键词'),
            Forms\Components\Toggle::make('maintenance_mode')
                ->label('维护模式')
                ->helperText('启用后网站将进入维护状态，访客将看到维护页面'),
        ];
    }

    public function save(): void
    {
        Setting::set('site_name', $this->site_name);
        Setting::set('site_description', $this->site_description);
        Setting::set('site_logo', $this->site_logo);
        Setting::set('contact_email', $this->contact_email);
        Setting::set('contact_phone', $this->contact_phone);
        Setting::set('contact_address', $this->contact_address);
        Setting::set('social_facebook', $this->social_facebook);
        Setting::set('social_twitter', $this->social_twitter);
        Setting::set('social_instagram', $this->social_instagram);
        Setting::set('social_linkedin', $this->social_linkedin);
        Setting::set('seo_title', $this->seo_title);
        Setting::set('seo_description', $this->seo_description);
        Setting::set('seo_keywords', $this->seo_keywords);
        Setting::set('maintenance_mode', $this->maintenance_mode);

        Notification::make()
            ->title('修改成功')
            ->success()
            ->send();
    }
}