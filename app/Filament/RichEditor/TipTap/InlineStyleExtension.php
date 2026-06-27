<?php

namespace App\Filament\RichEditor\TipTap;

use Tiptap\Core\Extension;

class InlineStyleExtension extends Extension
{
    public static $name = 'inlineStyle';

    public function addOptions(): array
    {
        return [
            'types' => [
                'paragraph',
                'heading',
                'bulletList',
                'orderedList',
                'listItem',
                'blockquote',
                'codeBlock',
                'hardBreak',
                'horizontalRule',
                'table',
                'tableCell',
                'tableHeader',
                'tableRow',
                'image',
                'grid',
                'gridColumn',
                'details',
                'detailsSummary',
                'detailsContent',
                'lead',
                'small',
                'div',
            ],
        ];
    }

    public function addGlobalAttributes(): array
    {
        return [
            [
                'types' => $this->options['types'],
                'attributes' => [
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
                ],
            ],
        ];
    }
}
