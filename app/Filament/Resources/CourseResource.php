<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use App\Support\CourseSlug;
use App\Support\RichContent;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = '课程管理';

    protected static ?int $navigationSort = 10;

    protected static string|\UnitEnum|null $navigationGroup = '内容管理';

    protected static ?string $modelLabel = '课程';

    protected static ?string $pluralModelLabel = '课程';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('category_id')
                    ->label('课程分类')
                    ->options(Course::CATEGORY_OPTIONS)
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('title')
                    ->label('标题')
                    ->required()
                    ->maxLength(500)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                        if (filled(trim((string) $get('slug'))) || blank($state)) {
                            return;
                        }

                        $set('slug', CourseSlug::fromTitle($state));
                    })
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->label('URL 标识')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('留空时可由标题自动生成'),
                Forms\Components\TextInput::make('article_url')
                    ->label('文章链接')
                    ->maxLength(2048)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('registration_url')
                    ->label('报名链接')
                    ->maxLength(2048)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('city')
                    ->label('举办城市')
                    ->maxLength(120),
                Forms\Components\DatePicker::make('starts_at')
                    ->label('开课时间')
                    ->native(false),
                Forms\Components\DatePicker::make('registration_deadline')
                    ->label('报名截止时间')
                    ->native(false),
                Forms\Components\TextInput::make('cpd_credits')
                    ->label('获得学分')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.1)
                    ->helperText('支持一位小数，例如：6 或 6.5'),
                Forms\Components\TextInput::make('price')
                    ->label('价格')
                    ->maxLength(120)
                    ->helperText('可填数字或文字，如 0、免费'),
                Forms\Components\DatePicker::make('legacy_added_at')
                    ->label('添加时间')
                    ->default(fn (): string => now()->toDateString())
                    ->native(false),
                RichContent::configureFileAttachments(
                    Forms\Components\RichEditor::make('content')
                        ->label('内容')
                        ->columnSpanFull()
                        ->toolbarButtons(RichContent::pageToolbar()),
                )->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->numeric()
                    ->default(fn (): int => Course::defaultSortOrderForNew())
                    ->helperText('默认预填预计的课程 ID，保存后会自动对齐；数值越大越靠前')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否开通')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->sortable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('category_id')
                    ->label('分类')
                    ->formatStateUsing(fn (?int $state): string => Course::CATEGORY_OPTIONS[$state] ?? '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('城市')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('开课时间')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_deadline')
                    ->label('报名截止')
                    ->date()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cpd_credits')
                    ->label('学分')
                    ->formatStateUsing(fn ($state): string => Course::formatCpdCredits($state) ?? '—')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('开通')
                    ->boolean(),
                Tables\Columns\TextColumn::make('legacy_added_at')
                    ->label('添加时间')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('分类')
                    ->options(Course::CATEGORY_OPTIONS),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('是否开通'),
            ])
            ->actions([
                Actions\EditAction::make()->label('编辑'),
                Actions\DeleteAction::make()->label('删除'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()->label('批量删除'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
