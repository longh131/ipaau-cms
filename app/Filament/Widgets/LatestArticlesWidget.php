<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\TableWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;

class LatestArticlesWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '最近文章';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Article::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->limit(50),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('栏目'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }
}
