<?php

namespace App\Support;

use App\Models\IpaMember;

class CertificateLookupService
{
    /** @var array<int, string> */
    public const RECOGNIZED_PROJECT_TAGS = [
        '税务管理师',
        '财务管理师',
        'AI财务管理师',
    ];

    /**
     * @return array{
     *     status: string,
     *     message?: string,
     *     full_name?: string,
     *     project_names?: string,
     *     queried_at?: \Illuminate\Support\Carbon
     * }
     */
    public function lookup(string $fullName, string $memberNumber): array
    {
        $fullName = trim($fullName);
        $memberNumber = trim($memberNumber);

        if ($fullName === '' || $memberNumber === '') {
            return [
                'status' => 'invalid_input',
                'message' => '请填写会员姓名与证书编号。',
            ];
        }

        $member = IpaMember::query()
            ->where('member_number', $memberNumber)
            ->where('full_name', $fullName)
            ->first();

        if ($member === null) {
            return [
                'status' => 'credentials_mismatch',
                'message' => '会员姓名或证书编号输入错误',
            ];
        }

        $projectNames = $this->matchedProjectTags($member->member_tags);

        if ($projectNames === []) {
            return [
                'status' => 'not_found',
            ];
        }

        return [
            'status' => 'found',
            'full_name' => (string) $member->full_name,
            'project_names' => implode('，', $projectNames),
            'queried_at' => now(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function matchedProjectTags(mixed $memberTags): array
    {
        $haystack = trim((string) $memberTags);

        if ($haystack === '') {
            return [];
        }

        $matched = [];

        foreach (self::RECOGNIZED_PROJECT_TAGS as $tag) {
            if (str_contains($haystack, $tag)) {
                $matched[] = $tag;
            }
        }

        return $matched;
    }
}
