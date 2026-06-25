<?php

namespace App\Filament\Widgets;

use App\Models\FormSubmission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentFormSubmissionsWidget extends TableWidget
{
    protected static ?string $heading = '最近表单提交';

    protected int|string|array $columnSpan = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FormSubmission::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('form_type')
                    ->label('表单类型'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('提交时间')
                    ->since(),
            ])
            ->paginated(false);
    }
}
