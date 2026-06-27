<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use App\Filament\Forms\ImageUpload;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class TestimonialsSectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('会员推荐')
                ->visible(fn (Get $get): bool => $get('component_type') === 'testimonials')
                ->statePath('data')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('推荐列表')
                        ->helperText('轮播展示；标题支持换行，前台按行显示。至少填写标题或内容之一。')
                        ->schema([
                            Forms\Components\Textarea::make('title')
                                ->label('标题')
                                ->rows(3)
                                ->helperText('可换行，每行单独显示')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('content')
                                ->label('内容')
                                ->rows(5)
                                ->columnSpanFull(),
                            ImageUpload::make(
                                'image',
                                'page-components/testimonials',
                                '图片',
                                '建议正方形头像（如 400×400）',
                            )->columnSpanFull(),
                        ])
                        ->minItems(0)
                        ->maxItems(12)
                        ->reorderable()
                        ->addActionLabel('添加推荐')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
