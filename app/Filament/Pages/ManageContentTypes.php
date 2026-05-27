<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageContentTypes extends Page
{
    protected static ?string $title = '类型管理';

    protected static ?string $navigationLabel = '类型管理';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cog6Tooth;
    
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.manage-content-types';

    public array $enabledTypes = [];

    public function mount(): void
    {
        $this->enabledTypes = Setting::get('enabled_content_types', ['article', 'page', 'link', 'member']);
    }

    public function save(): void
    {
        Setting::set('enabled_content_types', $this->enabledTypes);
        $this->dispatch('saved');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('保存设置')
                ->action(function () {
                    $this->save();
                }),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\CheckboxList::make('enabledTypes')
                ->label('启用的内容类型')
                ->options(Category::getTypeOptions())
                ->columns(3),
        ];
    }
}