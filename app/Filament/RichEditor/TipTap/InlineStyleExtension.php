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

    /**
     * 图片宽高由 width/height 属性生成，剔除全局 style 里的 width/height，
     * 避免旧值残留并在每次保存时重复叠加（如 `width: 800; width: 800;`）。
     */
    protected function stripSizeFromStyle(?string $style): ?string
    {
        if (blank($style)) {
            return null;
        }

        $rest = array_values(array_filter(
            array_map('trim', explode(';', $style)),
            fn (string $declaration): bool => $declaration !== '' && ! preg_match('/^(width|height)\s*:/i', $declaration),
        ));

        return $rest === [] ? null : implode('; ', $rest).';';
    }

    public function addGlobalAttributes(): array
    {
        return [
            [
                'types' => ['image'],
                'attributes' => [
                    'style' => [
                        'default' => null,
                        'parseHTML' => fn ($DOMNode) => $this->stripSizeFromStyle($DOMNode->getAttribute('style')),
                        'renderHTML' => function ($attributes) {
                            $style = $this->stripSizeFromStyle($attributes->style ?? null);

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
            [
                'types' => array_values(array_diff($this->options['types'], ['image'])),
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
