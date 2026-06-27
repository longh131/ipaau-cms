<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

class AboutIntroSectionForm
{
    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function schema(): array
    {
        return BasicContentSectionForm::schema(
            componentType: 'about-intro',
            sectionTitle: '关于 IPA 内容',
            titleHeadingTag: 'H2',
            maxButtons: 10,
        );
    }
}
