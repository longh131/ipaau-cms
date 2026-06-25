<?php

namespace App\Filament\Resources;

use App\Models\FormSubmission;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::DocumentCheck;

    protected static ?string $navigationLabel = '表单提交';

    protected static ?string $modelLabel = '表单提交';

    protected static ?string $pluralModelLabel = '表单提交';

    protected static ?int $navigationSort = 52;

    protected static string|\UnitEnum|null $navigationGroup = '系统';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('form_type')
                    ->label('表单类型')
                    ->formatStateUsing(function ($state) {
                        $types = [
                            'online_application' => '在线申请',
                            'contribution' => '投稿',
                            'student_member' => '学生会员',
                        ];
                        return $types[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('data')
                    ->label('提交数据')
                    ->formatStateUsing(function ($state) {
                        return json_encode($state, JSON_PRETTY_PRINT);
                    }),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP地址'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('提交时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('form_type')
                    ->label('表单类型')
                    ->options([
                        'online_application' => '在线申请',
                        'contribution' => '投稿',
                        'student_member' => '学生会员',
                    ]),
            ])
            ->actions([
            ])
            ->bulkActions([
                Actions\ExportBulkAction::make()
                    ->label('导出'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\FormSubmissionResource\Pages\ListFormSubmissions::route('/'),
        ];
    }
}