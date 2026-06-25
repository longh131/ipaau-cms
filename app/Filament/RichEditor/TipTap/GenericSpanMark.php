<?php

namespace App\Filament\RichEditor\TipTap;

use Tiptap\Core\Mark;

class GenericSpanMark extends Mark
{
    public static $name = 'genericSpan';

    public static $priority = 1000;

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'span',
                'getAttrs' => function ($DOMNode) {
                    $style = $DOMNode->getAttribute('style');
                    $class = $DOMNode->getAttribute('class');

                    if (blank($style) && blank($class)) {
                        return false;
                    }

                    return array_filter([
                        'style' => $style ?: null,
                        'class' => $class ?: null,
                    ]);
                },
            ],
        ];
    }

    public function addAttributes(): array
    {
        return [
            'style' => [
                'default' => null,
            ],
            'class' => [
                'default' => null,
            ],
        ];
    }

    /**
     * @param  object  $mark
     * @param  array<string, mixed>  $HTMLAttributes
     * @return array<mixed>
     */
    public function renderHTML($mark, $HTMLAttributes = []): array
    {
        foreach (['style', 'class'] as $attribute) {
            $value = $mark->attrs->{$attribute} ?? null;

            if (filled($value)) {
                $HTMLAttributes[$attribute] = $value;
            }
        }

        return ['span', $HTMLAttributes, 0];
    }
}
