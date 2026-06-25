<?php

namespace App\Filament\RichEditor\TipTap;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class DivNode extends Node
{
    public static $name = 'div';

    public static $priority = 1000;

    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [],
        ];
    }

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'div',
            ],
        ];
    }

    public function renderHTML($node, $HTMLAttributes = []): array
    {
        return ['div', HTML::mergeAttributes($this->options['HTMLAttributes'], $HTMLAttributes), 0];
    }
}
