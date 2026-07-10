<?php

namespace App\Filament\Resources\PageResource\Forms;

use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

class BasicContentPageForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('页面内容')
                ->description('前台依次显示：标题、摘要、正文（HTML 原样输出）。')
                ->statePath('data')
                ->schema([
                    Forms\Components\TextInput::make('heading')
                        ->label('标题')
                        ->placeholder('例如：Public Practice')
                        ->helperText('显示在前台页面顶部；留空则使用上方「页面标题」')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('summary')
                        ->label('摘要')
                        ->rows(4)
                        ->helperText('显示在标题下方，用于引导性说明')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('body')
                        ->label('正文（HTML 源码）')
                        ->rows(24)
                        ->helperText('直接粘贴或编写 HTML，保存后前台原样渲染；可使用 div、flex 等布局，不会被富文本编辑器改写。')
                        ->columnSpanFull()
                        ->extraInputAttributes([
                            'class' => 'font-mono text-sm',
                            'spellcheck' => 'false',
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }
}
