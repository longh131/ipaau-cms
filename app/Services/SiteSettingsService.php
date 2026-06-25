<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\RichContent;

class SiteSettingsService
{
    public const FOOTER_SOCIAL_ORDER = [
        'linkedin',
        'douyin',
        'xiaohongshu',
        'wechat_channels',
    ];

    private const SOCIAL_PLATFORMS = [
        'linkedin' => ['label' => 'LinkedIn', 'icon' => 'linkedin.svg'],
        'douyin' => ['label' => '抖音', 'icon' => 'douyin.svg'],
        'xiaohongshu' => ['label' => '小红书', 'icon' => 'xiaohongshu.svg'],
        'wechat_channels' => ['label' => '视频号', 'icon' => 'wechat-channels.svg'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'facebook.svg'],
        'twitter' => ['label' => 'Twitter', 'icon' => 'twitter.svg'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'instagram.svg'],
        'youtube' => ['label' => 'YouTube', 'icon' => 'youtube.svg'],
    ];

    public function getFooterDisclaimer(): string
    {
        $default = '<p>我们是一家全球领先的会计专业组织，专注服务中小企业（SME）领域，代表50,000余名会员及学员。请使用预留协会的有效手机号扫码「IPA服务」，浏览手机端微信会员中心。</p>';
        $content = Setting::get('footer_disclaimer');

        return RichContent::toHtml($content ?? $default);
    }

    public function getFooterCopyright(): string
    {
        $default = '<div>© {year} Institute of Public Accountants <span class="text-warm-plum">Copyright</span></div>';
        $content = Setting::get('footer_copyright');
        $html = RichContent::toHtml($content ?? $default);

        return str_replace('{year}', (string) date('Y'), $html);
    }

    /**
     * @return array<int, array{key: string, label: string, url: string, icon: string}>
     */
    public function getFooterSocialLinks(): array
    {
        $links = [];

        foreach (self::FOOTER_SOCIAL_ORDER as $key) {
            if (! $this->isSocialEnabled($key)) {
                continue;
            }

            $platform = self::SOCIAL_PLATFORMS[$key];
            $url = $this->getSocialUrl($key);

            $links[] = [
                'key' => $key,
                'label' => $platform['label'],
                'url' => $url,
                'icon' => asset('assets/svg/social/'.$platform['icon']),
            ];
        }

        return $links;
    }

    public function isSocialEnabled(string $key): bool
    {
        return $this->toBoolean(
            Setting::get("social_{$key}_enabled"),
            $key === 'linkedin',
        );
    }

    public function getSocialUrl(string $key): ?string
    {
        $value = Setting::get("social_{$key}", '');

        if (is_array($value)) {
            $value = '';
        }

        $url = trim((string) $value);

        return filled($url) ? $url : null;
    }

    private function toBoolean(mixed $value, bool $default = false): bool
    {
        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }

            if (in_array($normalized, ['0', 'false', 'no', 'off', ''], true)) {
                return false;
            }
        }

        return (bool) $value;
    }

    /**
     * @return array<string, array{label: string, icon: string}>
     */
    public function socialPlatformDefinitions(): array
    {
        return self::SOCIAL_PLATFORMS;
    }
}
