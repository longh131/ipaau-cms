<?php

namespace App\Filament\RichEditor\TipTap;

use Tiptap\Core\Extension;

class ImageFloatExtension extends Extension
{
    public static $name = 'imageFloat';

    public function addGlobalAttributes(): array
    {
        return [
            [
                'types' => ['image'],
                'attributes' => [
                    'float' => [
                        'default' => null,
                        'parseHTML' => function ($DOMNode): ?string {
                            $float = $DOMNode->getAttribute('data-float');

                            if (in_array($float, ['left', 'right'], true)) {
                                return $float;
                            }

                            $class = $DOMNode->getAttribute('class');

                            if (str_contains($class, 'cms-img-float-left')) {
                                return 'left';
                            }

                            if (str_contains($class, 'cms-img-float-right')) {
                                return 'right';
                            }

                            return null;
                        },
                        'renderHTML' => function ($attributes): ?array {
                            $float = $attributes->float ?? null;

                            if (! in_array($float, ['left', 'right'], true)) {
                                return null;
                            }

                            return [
                                'data-float' => $float,
                                'class' => 'cms-img-float-'.$float,
                            ];
                        },
                    ],
                ],
            ],
        ];
    }
}
