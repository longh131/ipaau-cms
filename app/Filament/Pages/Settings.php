<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => Setting::get('site_name', ''),
            'site_description' => Setting::get('site_description', ''),
            'site_logo' => Setting::get('site_logo', ''),
            'contact_email' => Setting::get('contact_email', ''),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'social_facebook' => Setting::get('social_facebook', ''),
            'social_twitter' => Setting::get('social_twitter', ''),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_linkedin' => Setting::get('social_linkedin', ''),
            'seo_title' => Setting::get('seo_title', ''),
            'seo_description' => Setting::get('seo_description', ''),
            'seo_keywords' => Setting::get('seo_keywords', ''),
            'maintenance_mode' => Setting::get('maintenance_mode', false),
        ]);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
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
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        $this->notify('success', '设置已保存');
    }
}