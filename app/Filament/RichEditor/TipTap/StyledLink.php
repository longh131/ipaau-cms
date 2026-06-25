<?php

namespace App\Filament\RichEditor\TipTap;

use Tiptap\Marks\Link;

class StyledLink extends Link
{
    public static $priority = 1100;

    public function addAttributes(): array
    {
        return [
            ...parent::addAttributes(),
            'style' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('style') ?: null,
                'renderHTML' => function ($attributes) {
                    $style = $attributes->style ?? null;

                    if (blank($style)) {
                        return null;
                    }

                    return ['style' => $style];
                },
            ],
            'class' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('class') ?: null,
                'renderHTML' => function ($attributes) {
                    $class = $attributes->class ?? null;

                    if (blank($class)) {
                        return null;
                    }

                    return ['class' => $class];
                },
            ],
        ];
    }
}
