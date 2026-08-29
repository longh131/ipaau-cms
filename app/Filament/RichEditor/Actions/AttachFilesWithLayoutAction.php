<?php

namespace App\Filament\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;
use Livewire\Component;

class AttachFilesWithLayoutAction
{
    public const DEFAULT_IMAGE_WIDTH = 800;

    /**
     * @return array<string, string>
     */
    public static function floatOptions(): array
    {
        return [
            '' => '默认（不浮动）',
            'left' => '左浮动（文字在右侧绕排）',
            'right' => '右浮动（文字在左侧绕排）',
        ];
    }

    public static function normalizeFloat(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return in_array($value, ['left', 'right'], true) ? $value : null;
    }

    public static function floatFromClass(mixed $class): ?string
    {
        if (! is_string($class) || $class === '') {
            return null;
        }

        if (str_contains($class, 'cms-img-float-left')) {
            return 'left';
        }

        if (str_contains($class, 'cms-img-float-right')) {
            return 'right';
        }

        return null;
    }

    public static function normalizeWidth(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $width = (int) $value;

            return $width > 0 ? $width : null;
        }

        if (is_string($value) && preg_match('/^(\d+)/', $value, $matches)) {
            $width = (int) $matches[1];

            return $width > 0 ? $width : null;
        }

        return null;
    }

    public static function shouldLimitWidthByDefault(array $arguments): bool
    {
        if (blank($arguments['src'] ?? null)) {
            return true;
        }

        return filled(static::normalizeWidth($arguments['width'] ?? null));
    }

    /**
     * @return array<string, mixed>
     */
    public static function imageAttributes(?string $alt, ?string $id, ?string $src, ?string $float, ?int $width = null): array
    {
        return [
            'alt' => $alt,
            'id' => $id,
            'src' => $src,
            'float' => $float,
            'width' => $width,
        ];
    }

    public static function make(): Action
    {
        return Action::make('attachFiles')
            ->label(__('filament-forms::components.rich_editor.actions.attach_files.label'))
            ->modalHeading(__('filament-forms::components.rich_editor.actions.attach_files.modal.heading'))
            ->modalWidth(Width::Large)
            ->fillForm(fn (array $arguments): array => [
                'alt' => $arguments['alt'] ?? null,
                'float' => static::normalizeFloat($arguments['float'] ?? null)
                    ?? static::floatFromClass($arguments['class'] ?? null)
                    ?? '',
                'limit_width' => static::shouldLimitWidthByDefault($arguments),
                'width' => static::normalizeWidth($arguments['width'] ?? null) ?? static::DEFAULT_IMAGE_WIDTH,
            ])
            ->schema(fn (array $arguments, RichEditor $component): array => [
                FileUpload::make('file')
                    ->label(filled($arguments['src'] ?? null)
                        ? __('filament-forms::components.rich_editor.actions.attach_files.modal.form.file.label.existing')
                        : __('filament-forms::components.rich_editor.actions.attach_files.modal.form.file.label.new'))
                    ->acceptedFileTypes($component->getFileAttachmentsAcceptedFileTypes())
                    ->maxSize($component->getFileAttachmentsMaxSize())
                    ->storeFiles(false)
                    ->required(blank($arguments['src'] ?? null))
                    ->hiddenLabel(blank($arguments['src'] ?? null)),
                TextInput::make('alt')
                    ->label(filled($arguments['src'] ?? null)
                        ? __('filament-forms::components.rich_editor.actions.attach_files.modal.form.alt.label.existing')
                        : __('filament-forms::components.rich_editor.actions.attach_files.modal.form.alt.label.new'))
                    ->maxLength(1000),
                Select::make('float')
                    ->label('图片布局')
                    ->options(static::floatOptions())
                    ->default('')
                    ->native(false),
                Toggle::make('limit_width')
                    ->label('固定图片宽度')
                    ->default(true)
                    ->live(),
                TextInput::make('width')
                    ->label('图片宽度')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5000)
                    ->default(static::DEFAULT_IMAGE_WIDTH)
                    ->suffix('px')
                    ->visible(fn (Get $get): bool => (bool) $get('limit_width')),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component, Component $livewire): void {
                $float = static::normalizeFloat($data['float'] ?? null);
                $width = ($data['limit_width'] ?? false)
                    ? (static::normalizeWidth($data['width'] ?? static::DEFAULT_IMAGE_WIDTH) ?? static::DEFAULT_IMAGE_WIDTH)
                    : null;

                if ($data['file'] ?? null) {
                    $id = (string) Str::orderedUuid();

                    data_set($livewire, "componentFileAttachments.{$component->getStatePath()}.{$id}", $data['file']);
                    $src = $component->getUploadedFileAttachmentTemporaryUrl($data['file']);
                }

                if (filled($arguments['src'] ?? null)) {
                    if ($arguments['editorSelection']['type'] !== 'node') {
                        $arguments['editorSelection']['type'] = 'node';
                        $arguments['editorSelection']['anchor']--;

                        unset($arguments['editorSelection']['head']);
                    }

                    $id ??= $arguments['id'] ?? null;
                    $src ??= $arguments['src'];

                    $component->runCommands(
                        [
                            EditorCommand::make('updateAttributes', arguments: [
                                'image',
                                static::imageAttributes($data['alt'] ?? null, $id, $src, $float, $width),
                            ]),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );

                    return;
                }

                if (blank($id ?? null)) {
                    return;
                }

                if (blank($src ?? null)) {
                    return;
                }

                $component->runCommands(
                    [
                        EditorCommand::make('insertContent', arguments: [[
                            'type' => 'image',
                            'attrs' => static::imageAttributes($data['alt'] ?? null, $id, $src, $float, $width),
                        ]]),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
