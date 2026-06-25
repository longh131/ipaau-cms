<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageComponentResource\Forms\FootnoteCardsSectionForm;
use App\Filament\Resources\PageComponentResource\Forms\HeroSectionForm;
use App\Models\PageComponent;
use App\Support\HomeSectionTypes;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PageComponentResource extends Resource
{
    protected static ?string $model = PageComponent::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::HomeModern;

    protected static ?string $navigationLabel = '首页板块';

    protected static ?int $navigationSort = 10;

    protected static string|\UnitEnum|null $navigationGroup = '内容管理';

    protected static ?string $modelLabel = '首页板块';

    protected static ?string $pluralModelLabel = '首页板块';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(static::formComponents(includeKeyValue: true));
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function formComponents(bool $includeKeyValue = true): array
    {
        $components = [
            ...static::baseFormComponents(),
            ...HeroSectionForm::schema(),
            ...FootnoteCardsSectionForm::schema(),
        ];

        if ($includeKeyValue) {
            $components[] = Forms\Components\KeyValue::make('data')
                ->label('板块数据')
                ->keyLabel('字段')
                ->valueLabel('内容')
                ->reorderable()
                ->columnSpanFull()
                ->visible(fn (Get $get) => filled($get('component_type')) && ! HomeSectionTypes::isStructured((string) $get('component_type')))
                ->dehydrated(fn (Get $get) => filled($get('component_type')) && ! HomeSectionTypes::isStructured((string) $get('component_type')))
                ->helperText('JSON 键值；复杂结构（如 tabs、items）可存 JSON 字符串，前台接入时再解析');
        }

        $components[] = Forms\Components\TextInput::make('sort_order')
            ->label('排序')
            ->numeric()
            ->default(0)
            ->helperText('数字越小越靠前，需与前台 section 顺序一致');

        $components[] = Forms\Components\Toggle::make('is_active')
            ->label('是否启用')
            ->default(true);

        return $components;
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function baseFormComponents(): array
    {
        return [
            Forms\Components\Hidden::make('page_slug')
                ->default(HomeSectionTypes::PAGE_SLUG),
            Forms\Components\Select::make('component_type')
                ->label('板块类型')
                ->options(HomeSectionTypes::options())
                ->required()
                ->live()
                ->disabledOn('edit')
                ->afterStateUpdated(function (?string $state, Set $set) {
                    if ($state) {
                        $set('sort_order', HomeSectionTypes::defaultSortOrder($state));
                    }
                }),
            Forms\Components\Placeholder::make('section_hint')
                ->label('板块说明')
                ->content(fn (Get $get) => HomeSectionTypes::definitions()[$get('component_type')]['description'] ?? '请选择板块类型')
                ->visible(fn (Get $get) => filled($get('component_type'))),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->where('page_slug', HomeSectionTypes::PAGE_SLUG)
                ->orderBy('sort_order'))
            ->columns([
                Tables\Columns\TextColumn::make('component_type')
                    ->label('板块')
                    ->formatStateUsing(fn (string $state) => HomeSectionTypes::label($state))
                    ->description(fn (PageComponent $record) => HomeSectionTypes::definitions()[$record->component_type]['description'] ?? null),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('启用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('启用状态'),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('新增板块'),
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
            ])
            ->emptyStateHeading('暂无首页板块')
            ->emptyStateDescription('可运行 php artisan db:seed --class=HomeSectionSeeder 初始化 12 个默认板块');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PageComponentResource\Pages\ListPageComponents::route('/'),
            'create' => \App\Filament\Resources\PageComponentResource\Pages\CreatePageComponent::route('/create'),
            'edit' => \App\Filament\Resources\PageComponentResource\Pages\EditPageComponent::route('/{record}/edit'),
        ];
    }
}
