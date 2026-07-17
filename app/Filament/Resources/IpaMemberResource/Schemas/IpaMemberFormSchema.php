<?php

namespace App\Filament\Resources\IpaMemberResource\Schemas;

use App\Support\MemberFieldMap;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IpaMemberFormSchema
{
    public static function configure(Schema $schema): Schema
    {
        $components = [
            Section::make('基本信息')
                ->columns(3)
                ->schema(self::fields([
                    'member_number', 'salutation', 'full_name', 'first_name', 'last_name',
                    'gender', 'birth_date', 'mobile_phone', 'email', 'alternate_email',
                ])),
            Section::make('会籍信息')
                ->columns(3)
                ->schema(self::fields([
                    'membership_status', 'member_level', 'member_level_short', 'member_tags',
                    'joined_at', 'level_valid_until', 'membership_years', 'current_level_years',
                    'region', 'review_discount', 'special_approval',
                ])),
            Section::make('工作信息')
                ->columns(3)
                ->schema(self::fields([
                    'job_title_zh', 'job_title_en', 'company_name_zh', 'company_name_en',
                    'work_phone', 'home_phone', 'fax', 'wechat',
                    'other_social_platform', 'other_social_account',
                ])),
            Section::make('证件与证书')
                ->columns(3)
                ->schema(self::fields([
                    'id_type', 'id_number', 'certificate_name', 'certificate_printed',
                    'certificate_issued_at', 'exam_status', 'ifrs', 'ethics', 'bda', 'cpd_credits',
                ])),
            Section::make('推荐与联系人')
                ->columns(3)
                ->schema(self::fields([
                    'assistant_contact', 'assistant_phone', 'referrer_mobile',
                    'referrer_name', 'referrer_member_number', 'partner_level_1', 'partner_level_2',
                ])),
            Section::make('申请与变更记录')
                ->columns(3)
                ->schema(self::fields([
                    'membership_restored_at', 'membership_upgraded_at', 'membership_transferred_at',
                    'leave_reason', 'leave_expires_at', 'termination_or_leave_reason', 'termination_or_leave_at',
                    'membership_application_at', 'membership_application_type', 'membership_application_status',
                    'membership_application_status_at', 'membership_application_review_at', 'membership_application_reviewer',
                ])),
            Section::make('备注')
                ->schema([
                    self::field('notes')->columnSpanFull(),
                ]),
        ];

        return $schema->components($components);
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    private static function fields(array $fields): array
    {
        return array_map(fn (string $field) => self::field($field), $fields);
    }

    private static function field(string $field): \Filament\Schemas\Components\Component
    {
        $label = MemberFieldMap::DB_LABELS[$field] ?? $field;

        if (in_array($field, MemberFieldMap::dateFields(), true)) {
            return Forms\Components\DatePicker::make($field)->label($label);
        }

        if ($field === 'notes' || $field === 'leave_reason' || $field === 'termination_or_leave_reason' || $field === 'member_tags') {
            return Forms\Components\Textarea::make($field)->label($label)->rows(3);
        }

        if ($field === 'member_number') {
            return Forms\Components\TextInput::make($field)
                ->label($label)
                ->required()
                ->unique(ignoreRecord: true);
        }

        return Forms\Components\TextInput::make($field)->label($label);
    }
}
