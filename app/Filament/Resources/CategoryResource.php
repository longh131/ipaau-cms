<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Schema as SchemaFacade;
use App\Models\Category;
use App\Models\Setting;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Folder;

    protected static ?string $navigationLabel = '栏目管理';
    
    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = '栏目';

    protected static ?string $pluralModelLabel = '栏目';

    protected static function getEnabledTypeOptions(): array
    {
        $allTypes = Category::getTypeOptions();

        if (!SchemaFacade::hasTable('settings')) {
            return $allTypes;
        }

        $enabledTypes = Setting::get('enabled_content_types', ['article', 'page', 'link', 'member']);

        return array_filter($allTypes, fn ($key) => in_array($key, $enabledTypes), ARRAY_FILTER_USE_KEY);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('名称')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('别名')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('parent_id')
                    ->label('上级栏目')
                    ->options(function () {
                        return [0 => '无（作为顶级栏目）'] + Category::where('parent_id', 0)
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->default(0),
                Forms\Components\Select::make('type')
                    ->label('类型')
                    ->options(static::getEnabledTypeOptions())
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('name')
                    ->label('名称')
                    ->view('filament.resources.category.columns.tree-name'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('别名'),
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->formatStateUsing(function ($state) {
                        return Category::getTypeOptions()[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->orderByRaw('
                    CASE WHEN parent_id = 0 THEN id ELSE parent_id END,
                    parent_id != 0,
                    sort_order
                ');
            })
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('类型')
                    ->options(static::getEnabledTypeOptions()),
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
            'index' => \App\Filament\Resources\CategoryResource\Pages\ListCategories::route('/'),
            'create' => \App\Filament\Resources\CategoryResource\Pages\CreateCategory::route('/create'),
            'edit' => \App\Filament\Resources\CategoryResource\Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}