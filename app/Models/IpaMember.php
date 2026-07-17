<?php

namespace App\Models;

use App\Support\MemberFieldMap;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class IpaMember extends Model
{
    protected $fillable;

    protected function casts(): array
    {
        $dates = [];

        foreach (MemberFieldMap::dateFields() as $field) {
            $dates[$field] = 'date';
        }

        return $dates;
    }

    public function __construct(array $attributes = [])
    {
        $this->fillable = array_keys(MemberFieldMap::DB_LABELS);

        parent::__construct($attributes);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([
            $this->full_name,
            trim(($this->first_name ?? '').' '.($this->last_name ?? '')),
        ]);

        return $parts[0] ?? $this->member_number;
    }

    public function getPortalTitleAttribute(): string
    {
        $name = $this->display_name;
        $level = $this->member_level_short ?: $this->member_level;

        return $level ? "{$name}, {$level}" : $name;
    }

    public function canLoginViaSms(): bool
    {
        return filled($this->mobile_phone);
    }

    public function isMembershipExpired(): bool
    {
        if (! $this->level_valid_until instanceof Carbon) {
            return false;
        }

        return $this->level_valid_until->isPast();
    }

    public function membershipExpiryLabel(): ?string
    {
        if (! $this->level_valid_until instanceof Carbon) {
            return null;
        }

        return $this->level_valid_until->format('n月j日');
    }

    public function membershipExpiryNotice(): ?array
    {
        $label = $this->membershipExpiryLabel();

        if ($label === null) {
            return null;
        }

        if ($this->isMembershipExpired()) {
            return [
                'status' => 'expired',
                'message' => "会籍资格已于{$label}过期",
            ];
        }

        return [
            'status' => 'active',
            'message' => "会员级别有效期至{$label}",
        ];
    }

    public function ageGroup(): ?string
    {
        if (! $this->birth_date instanceof Carbon) {
            return null;
        }

        $age = $this->birth_date->age;

        return match (true) {
            $age < 30 => '小于30岁',
            $age < 40 => '30岁-40岁',
            $age < 50 => '40岁-50岁',
            default => '大于50岁',
        };
    }
}
