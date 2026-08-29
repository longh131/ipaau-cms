<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\HtmlString;

class LearningPlatformSettingsService
{
    public const PLATFORM_A = 'a';

    /** @var array<string, string> */
    private const PLATFORM_A_FIELDS = [
        'api_code' => 'learning_platform_a_api_code',
        'api_secret' => 'learning_platform_a_api_secret',
        'api_url' => 'learning_platform_a_api_url',
        'sso_login_url' => 'learning_platform_a_sso_login_url',
        'default_jump_url' => 'learning_platform_a_default_jump_url',
    ];

    /**
     * @return array{
     *     api_code: string,
     *     api_secret: string,
     *     api_url: string,
     *     sso_login_url: string,
     *     default_jump_url: string
     * }
     */
    public function getPlatformAConfig(): array
    {
        $config = [];

        foreach (self::PLATFORM_A_FIELDS as $field => $settingKey) {
            $config[$field] = $this->resolvePlatformAValue($field, $settingKey);
        }

        return $config;
    }

    /**
     * @return array<string, string>
     */
    public function getPlatformAFormDefaults(): array
    {
        return $this->getPlatformAConfig();
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function savePlatformAConfig(array $state): void
    {
        foreach (self::PLATFORM_A_FIELDS as $field => $settingKey) {
            $value = trim((string) ($state["learning_platform_a_{$field}"] ?? ''));

            if ($field === 'api_secret' && $value === '') {
                $existing = Setting::get($settingKey);
                if (is_string($existing) && trim($existing) !== '') {
                    continue;
                }

                $value = $this->resolvePlatformAValue($field, $settingKey);
            }

            if ($field === 'api_url' || $field === 'sso_login_url' || $field === 'default_jump_url') {
                $value = $this->normalizeHttpsUrl($value);
            }

            Setting::set($settingKey, $value);
        }
    }

    public function getPlatformADefaultJumpUrl(): string
    {
        return $this->getPlatformAConfig()['default_jump_url'];
    }

    public function platformALinkUsageHtml(): HtmlString
    {
        $base = '/member/bolue-sso';
        $encodedLiveExample = $base.'?jumpUrl='.rawurlencode('https://www.bolue.cn/lives/1695?productId=9015');

        $lines = [
            '以下链接用于文章、栏目或按钮（使用站内相对路径，换域名无需修改）。会员点击后需已登录；未登录会先跳转会员登录，再自动进入铂略。',
            '',
            '【默认录播课包 · 等同旧站 GetBLAuthCode.aspx】',
            $base,
            $base.'?target=course-pack',
            '',
            '【指定铂略页面】',
            'jumpUrl 须为 https，且域名仅限 bolue.cn / www.bolue.cn。',
            $base.'?jumpUrl=https://www.bolue.cn/lives/1695?productId=9015',
            '',
            '【URL 编码示例（粘贴到富文本/HTML 时）】',
            $encodedLiveExample,
        ];

        $body = implode("\n", array_map(
            static fn (string $line): string => e($line),
            $lines,
        ));

        return new HtmlString(
            '<pre class="whitespace-pre-wrap rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm leading-6 text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">'.$body.'</pre>'
        );
    }

    private function resolvePlatformAValue(string $field, string $settingKey): string
    {
        $stored = Setting::get($settingKey);

        if (is_string($stored) && trim($stored) !== '') {
            return trim($stored);
        }

        if (is_numeric($stored) && (string) $stored !== '') {
            return trim((string) $stored);
        }

        $envValue = config("bolue.{$field}");
        if (is_string($envValue) && trim($envValue) !== '') {
            return trim($envValue);
        }

        return trim((string) config("bolue.defaults.{$field}", ''));
    }

    private function normalizeHttpsUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return '';
        }

        if (preg_match('#^[a-z][a-z0-9+\-.]*://#i', $url)) {
            return $url;
        }

        return 'https://'.ltrim($url, '/');
    }
}
