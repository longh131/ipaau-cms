<?php

namespace App\Filament\Resources;

use App\Models\Form;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $navigationLabel = '表单页面';

    protected static ?int $navigationSort = 20;

    protected static string|\UnitEnum|null $navigationGroup = '内容管理';

    protected static ?string $modelLabel = '表单';

    protected static ?string $pluralModelLabel = '表单';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->label('标题')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('URL 标识')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\RichEditor::make('content')
                    ->label('说明')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->label('封面图片')
                    ->image(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否启用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('标题'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL 标识'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('是否启用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('是否启用'),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->label('编辑'),
                Actions\DeleteAction::make()
                    ->label('删除'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->label('批量删除'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\FormResource\Pages\ListForms::route('/'),
            'create' => \App\Filament\Resources\FormResource\Pages\CreateForm::route('/create'),
            'edit' => \App\Filament\Resources\FormResource\Pages\EditForm::route('/{record}/edit'),
        ];
    }
}