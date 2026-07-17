<?php

namespace App\Services;

use App\Models\IpaMember;
use Illuminate\Support\Collection;

class MemberStatisticsService
{
    public function summary(): array
    {
        $members = IpaMember::query()->get([
            'gender',
            'birth_date',
            'member_level_short',
            'member_level',
            'region',
            'membership_status',
            'joined_at',
        ]);

        return [
            'total' => $members->count(),
            'gender' => $this->countBy($members, fn (IpaMember $m) => $m->gender ?: '未知'),
            'age_groups' => $this->ageGroups($members),
            'levels' => $this->memberLevels($members),
            'regions' => $this->countBy($members, fn (IpaMember $m) => $m->region ?: '未知'),
            'statuses' => $this->countBy($members, fn (IpaMember $m) => $m->membership_status ?: '未知'),
            'join_years' => $this->joinYears($members),
        ];
    }

    /**
     * @param  Collection<int, IpaMember>  $members
     * @return array<string, int>
     */
    private function ageGroups(Collection $members): array
    {
        $groups = [
            '小于30岁' => 0,
            '30岁-40岁' => 0,
            '40岁-50岁' => 0,
            '大于50岁' => 0,
            '未知' => 0,
        ];

        foreach ($members as $member) {
            $group = $member->ageGroup() ?? '未知';
            $groups[$group] = ($groups[$group] ?? 0) + 1;
        }

        return $groups;
    }

    /**
     * @param  Collection<int, IpaMember>  $members
     * @return array<string, int>
     */
    private function memberLevels(Collection $members): array
    {
        $levels = [];

        foreach ($members as $member) {
            $short = strtoupper(trim((string) $member->member_level_short));

            if (in_array($short, ['AIPA', 'MIPA', 'FIPA'], true)) {
                $key = $short;
            } else {
                $key = $member->member_level_short ?: ($member->member_level ?: '未知');
            }

            $levels[$key] = ($levels[$key] ?? 0) + 1;
        }

        arsort($levels);

        return $levels;
    }

    /**
     * @param  Collection<int, IpaMember>  $members
     * @return array<string, int>
     */
    private function joinYears(Collection $members): array
    {
        $years = [];

        foreach ($members as $member) {
            if ($member->joined_at === null) {
                continue;
            }

            $year = $member->joined_at->format('Y');
            $years[$year] = ($years[$year] ?? 0) + 1;
        }

        ksort($years);

        return $years;
    }

    /**
     * @param  Collection<int, IpaMember>  $members
     * @return array<string, int>
     */
    private function countBy(Collection $members, callable $resolver): array
    {
        $counts = [];

        foreach ($members as $member) {
            $key = (string) $resolver($member);
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        arsort($counts);

        return $counts;
    }
}
