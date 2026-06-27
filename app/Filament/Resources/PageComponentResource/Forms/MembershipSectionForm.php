<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

class MembershipSectionForm
{
    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function schema(): array
    {
        return BasicContentSectionForm::schema(
            componentType: 'membership',
            sectionTitle: '会员推广内容',
            titleHeadingTag: 'H2',
            maxButtons: 10,
        );
    }
}
