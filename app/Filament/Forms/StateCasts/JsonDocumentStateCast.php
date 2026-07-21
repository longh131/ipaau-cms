<?php

namespace App\Filament\Forms\StateCasts;

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

/**
 * 将 TipTap 文档在 Livewire 状态中存为 JSON 字符串，避免深层数组路径触发 nesting 限制。
 */
class JsonDocumentStateCast implements StateCast
{
    public function __construct(
        protected RichEditor $richEditor,
    ) {}

    /**
     * @return string|array<string, mixed>
     */
    public function get(mixed $state): string|array
    {
        if (is_array($state)) {
            $state = \App\Support\MediaUrl::normalizeRichContentValue($state);
        }

        if (is_string($state)) {
            $state = \App\Support\MediaUrl::normalizeRichContentValue($state);
        }

        if (is_string($state)) {
            return $state;
        }

        if (! is_array($state)) {
            return $this->emptyDocumentJson();
        }

        return json_encode($state, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     */
    public function set(mixed $state): array
    {
        if (is_array($state)) {
            return $state;
        }

        if (! is_string($state) || trim($state) === '') {
            return $this->emptyDocument();
        }

        $decoded = json_decode($state, true);

        return is_array($decoded) ? $decoded : $this->emptyDocument();
    }

    /**
     * @return array<string, mixed>
     */
    protected function emptyDocument(): array
    {
        return [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [],
                ],
            ],
        ];
    }

    protected function emptyDocumentJson(): string
    {
        return json_encode($this->emptyDocument(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
