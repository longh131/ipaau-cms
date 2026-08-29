<?php

namespace App\Filament\Pages;

use App\Filament\Forms\ImageUpload;
use App\Models\Setting;
use App\Services\LearningPlatformSettingsService;
use App\Support\RichContent;
use Illuminate\Support\Arr;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = '系统设置';

    protected static ?string $navigationLabel = '系统设置';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cog;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.settings';

    public ?string $site_name = '';

    public ?string $site_description = '';

    public ?string $site_logo = '';

    public ?string $contact_email = '';

    public ?string $contact_phone = '';

    public ?string $contact_address = '';

    /** @var array<string, mixed>|null */
    public ?array $footer_disclaimer = null;

    /** @var array<string, mixed>|null */
    public ?array $footer_copyright = null;

    public ?string $social_linkedin = '';

    public ?bool $social_linkedin_enabled = true;

    public ?string $social_douyin = '';

    public ?bool $social_douyin_enabled = false;

    public ?string $social_xiaohongshu = '';

    public ?bool $social_xiaohongshu_enabled = false;

    /** @var array<int, string>|string|null */
    public array|string|null $social_wechat_channels_qrcode = null;

    public ?bool $social_wechat_channels_enabled = false;

    /** @var array<int, string>|string|null */
    public array|string|null $social_wechat_qrcode = null;

    public ?bool $social_wechat_enabled = false;

    public ?string $social_facebook = '';

    public ?bool $social_facebook_enabled = false;

    public ?string $social_twitter = '';

    public ?bool $social_twitter_enabled = false;

    public ?string $social_instagram = '';

    public ?bool $social_instagram_enabled = false;

    public ?string $social_youtube = '';

    public ?bool $social_youtube_enabled = false;

    public ?string $seo_title = '';

    public ?string $seo_description = '';

    public ?string $seo_keywords = '';

    public ?bool $maintenance_mode = false;

    public ?string $events_cpd_registration_url = '';

    public ?string $learning_platform_a_api_code = '';

    public ?string $learning_platform_a_api_secret = '';

    public ?string $learning_platform_a_api_url = '';

    public ?string $learning_platform_a_sso_login_url = '';

    public ?string $learning_platform_a_default_jump_url = '';

    public function mount(LearningPlatformSettingsService $learningPlatformSettings): void
    {
        $defaultDisclaimer = '<p>我们是一家全球领先的会计专业组织，专注服务中小企业（SME）领域，代表50,000余名会员及学员。请使用预留协会的有效手机号扫码「IPA服务」，浏览手机端微信会员中心。</p>';
        $defaultCopyright = '<div>© {year} Institute of Public Accountants <span class="text-warm-plum">Copyright</span></div>';

        $platformA = $learningPlatformSettings->getPlatformAFormDefaults();

        $this->form->fill([
            'site_name' => Setting::get('site_name', ''),
            'site_description' => Setting::get('site_description', ''),
            'site_logo' => Setting::get('site_logo', ''),
            'contact_email' => Setting::get('contact_email', ''),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'footer_disclaimer' => RichContent::toDocument(Setting::get('footer_disclaimer', $defaultDisclaimer)),
            'footer_copyright' => RichContent::toDocument(Setting::get('footer_copyright', $defaultCopyright)),
            'social_linkedin' => Setting::get('social_linkedin', ''),
            'social_linkedin_enabled' => $this->normalizeBoolean(Setting::get('social_linkedin_enabled'), true),
            'social_douyin' => Setting::get('social_douyin', ''),
            'social_douyin_enabled' => $this->normalizeBoolean(Setting::get('social_douyin_enabled'), false),
            'social_xiaohongshu' => Setting::get('social_xiaohongshu', ''),
            'social_xiaohongshu_enabled' => $this->normalizeBoolean(Setting::get('social_xiaohongshu_enabled'), false),
            'social_wechat_channels_qrcode' => filled($channelsQrcode = Setting::get('social_wechat_channels_qrcode', '')) ? $channelsQrcode : null,
            'social_wechat_channels_enabled' => $this->normalizeBoolean(Setting::get('social_wechat_channels_enabled'), false),
            'social_wechat_qrcode' => filled($qrcode = Setting::get('social_wechat_qrcode', '')) ? $qrcode : null,
            'social_wechat_enabled' => $this->normalizeBoolean(Setting::get('social_wechat_enabled'), false),
            'social_facebook' => Setting::get('social_facebook', ''),
            'social_facebook_enabled' => $this->normalizeBoolean(Setting::get('social_facebook_enabled'), false),
            'social_twitter' => Setting::get('social_twitter', ''),
            'social_twitter_enabled' => $this->normalizeBoolean(Setting::get('social_twitter_enabled'), false),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_instagram_enabled' => $this->normalizeBoolean(Setting::get('social_instagram_enabled'), false),
            'social_youtube' => Setting::get('social_youtube', ''),
            'social_youtube_enabled' => $this->normalizeBoolean(Setting::get('social_youtube_enabled'), false),
            'seo_title' => Setting::get('seo_title', ''),
            'seo_description' => Setting::get('seo_description', ''),
            'seo_keywords' => Setting::get('seo_keywords', ''),
            'maintenance_mode' => $this->normalizeBoolean(Setting::get('maintenance_mode'), false),
            'events_cpd_registration_url' => Setting::get('events_cpd_registration_url', ''),
            'learning_platform_a_api_code' => $platformA['api_code'],
            'learning_platform_a_api_secret' => $platformA['api_secret'],
            'learning_platform_a_api_url' => $platformA['api_url'],
            'learning_platform_a_sso_login_url' => $platformA['sso_login_url'],
            'learning_platform_a_default_jump_url' => $platformA['default_jump_url'],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('保存设置')
                ->icon(Heroicon::Check)
                ->action('save'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('settingsTabs')
                ->tabs([
                    Tab::make('general')
                        ->label('网站信息')
                        ->icon(Heroicon::GlobeAlt)
                        ->schema([
                            Section::make('基本信息')
                                ->description('网站名称、Logo 与联系信息')
                                ->schema([
                                    Forms\Components\TextInput::make('site_name')
                                        ->label('网站名称')
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('site_logo')
                                        ->label('网站 Logo')
                                        ->placeholder('Logo 路径或 URL')
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('site_description')
                                        ->label('网站描述')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('contact_email')
                                        ->label('联系邮箱')
                                        ->email(),
                                    Forms\Components\TextInput::make('contact_phone')
                                        ->label('联系电话'),
                                    Forms\Components\Textarea::make('contact_address')
                                        ->label('联系地址')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),
                    Tab::make('footer')
                        ->label('页脚内容')
                        ->icon(Heroicon::RectangleStack)
                        ->schema([
                            Section::make('页脚文案')
                                ->description('底部导航请在「菜单管理 → 底部菜单」中配置。富文本编辑器可点击工具栏「源代码」按钮编辑 HTML。')
                                ->schema([
                                    Forms\Components\RichEditor::make('footer_disclaimer')
                                        ->label('页脚简介')
                                        ->columnSpanFull()
                                        ->toolbarButtons($this->footerRichEditorToolbar()),
                                    Forms\Components\RichEditor::make('footer_copyright')
                                        ->label('版权信息')
                                        ->helperText('支持链接、颜色样式；使用 {year} 自动替换为当前年份。')
                                        ->columnSpanFull()
                                        ->toolbarButtons($this->footerRichEditorToolbar()),
                                ]),
                        ]),
                    Tab::make('social')
                        ->label('社交媒体')
                        ->icon(Heroicon::Link)
                        ->schema([
                            Section::make('页脚显示顺序')
                                ->description('LinkedIn → 抖音 → 小红书 → 视频号 → 微信。开启「显示」后图标即出现在页脚；视频号与微信为悬停展示二维码，其他平台填写链接后可点击跳转。')
                                ->schema(array_merge(
                                    $this->socialPlatformFieldsets([
                                        'social_linkedin' => 'LinkedIn',
                                        'social_douyin' => '抖音',
                                        'social_xiaohongshu' => '小红书',
                                    ]),
                                    [
                                        $this->qrcodeSocialFieldset(
                                            label: '视频号',
                                            enabledKey: 'social_wechat_channels_enabled',
                                            qrcodeKey: 'social_wechat_channels_qrcode',
                                            helperText: '上传后在页脚悬停视频号图标时展示，无需填写链接。',
                                        ),
                                        $this->qrcodeSocialFieldset(
                                            label: '微信',
                                            enabledKey: 'social_wechat_enabled',
                                            qrcodeKey: 'social_wechat_qrcode',
                                            helperText: '上传后在页脚悬停微信图标时展示，无需填写链接。',
                                        ),
                                    ],
                                ))
                                ->columns(2),
                            Section::make('其他平台')
                                ->description('预留配置，当前页脚不显示以下平台图标。')
                                ->collapsed()
                                ->schema($this->socialPlatformFieldsets([
                                    'social_facebook' => 'Facebook',
                                    'social_twitter' => 'Twitter',
                                    'social_instagram' => 'Instagram',
                                    'social_youtube' => 'YouTube',
                                ]))
                                ->columns(2),
                        ]),
                    Tab::make('seo')
                        ->label('SEO 与维护')
                        ->icon(Heroicon::MagnifyingGlass)
                        ->schema([
                            Section::make('搜索引擎优化')
                                ->schema([
                                    Forms\Components\TextInput::make('seo_title')
                                        ->label('SEO 标题')
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('seo_description')
                                        ->label('SEO 描述')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('seo_keywords')
                                        ->label('SEO 关键词')
                                        ->columnSpanFull(),
                                ]),
                            Section::make('站点维护')
                                ->schema([
                                    Forms\Components\Toggle::make('maintenance_mode')
                                        ->label('维护模式')
                                        ->helperText('启用后网站将进入维护状态，访客将看到维护页面')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                    Tab::make('events_cpd')
                        ->label('活动与CPD')
                        ->icon(Heroicon::CalendarDays)
                        ->schema([
                            Section::make('默认报名链接')
                                ->description('活动与CPD 栏目下课程列表中，未单独填写「报名链接」的课程将使用此地址。')
                                ->schema([
                                    Forms\Components\TextInput::make('events_cpd_registration_url')
                                        ->label('链接地址')
                                        ->placeholder('https://')
                                        ->maxLength(2048)
                                        ->helperText('可省略 https://，保存时会自动补全')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                    Tab::make('learning_platform')
                        ->label('学习平台接口')
                        ->icon(Heroicon::AcademicCap)
                        ->schema([
                            Section::make('学习平台 A（铂略）接口参数')
                                ->description('用于会员 SSO 跳转铂略学习平台。保存后即时生效；若留空 API Secret 则保持原值不变。未填写时依次回退：已保存设置 → .env → 系统默认值。')
                                ->schema([
                                    Forms\Components\TextInput::make('learning_platform_a_api_code')
                                        ->label('API Code')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('learning_platform_a_api_secret')
                                        ->label('API Secret')
                                        ->password()
                                        ->revealable()
                                        ->helperText('留空表示不修改已保存的 Secret')
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('learning_platform_a_api_url')
                                        ->label('API URL')
                                        ->required()
                                        ->placeholder('https://papi.bolue.cn/openApi/getOpenSsoToken')
                                        ->maxLength(2048)
                                        ->helperText('可省略 https://，保存时会自动补全')
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('learning_platform_a_sso_login_url')
                                        ->label('SSO Login URL')
                                        ->required()
                                        ->placeholder('https://www.bolue.cn/uums/openSsoLogin')
                                        ->maxLength(2048)
                                        ->helperText('可省略 https://，保存时会自动补全')
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('learning_platform_a_default_jump_url')
                                        ->label('默认 Jump URL')
                                        ->required()
                                        ->placeholder('https://www.bolue.cn/products/9015')
                                        ->maxLength(2048)
                                        ->helperText('未指定 jumpUrl / target 时进入的铂略页面；可省略 https://')
                                        ->columnSpanFull(),
                                ]),
                            Section::make('学习平台 A 链接用法')
                                ->description('只读参考，复制到文章、栏目 HTML 或按钮链接中使用。')
                                ->schema([
                                    Forms\Components\Placeholder::make('learning_platform_a_link_usage')
                                        ->label('')
                                        ->content(fn (LearningPlatformSettingsService $learningPlatformSettings): \Illuminate\Support\HtmlString => $learningPlatformSettings->platformALinkUsageHtml())
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function footerRichEditorToolbar(): array
    {
        return [
            ['bold', 'italic', 'underline', 'link', 'textColor'],
            ['h2', 'h3', 'h4', 'bulletList', 'orderedList'],
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['clearFormat', 'undo', 'redo'],
            ['source-ai'],
        ];
    }

    /**
     * @param  array<string, string>  $platforms
     * @return array<int, Fieldset>
     */
    private function socialPlatformFieldsets(array $platforms): array
    {
        $fieldsets = [];

        foreach ($platforms as $key => $label) {
            $fieldsets[] = Fieldset::make($label)
                ->schema([
                    Forms\Components\Toggle::make("{$key}_enabled")
                        ->label('显示')
                        ->inline(false),
                    Forms\Components\TextInput::make($key)
                        ->label('链接')
                        ->placeholder('https://')
                        ->maxLength(2048)
                        ->helperText('可省略 https://，保存时会自动补全'),
                ])
                ->columns(1);
        }

        return $fieldsets;
    }

    private function qrcodeSocialFieldset(
        string $label,
        string $enabledKey,
        string $qrcodeKey,
        string $helperText,
    ): Fieldset {
        return Fieldset::make($label)
            ->schema([
                Forms\Components\Toggle::make($enabledKey)
                    ->label('显示')
                    ->inline(false),
                ImageUpload::make($qrcodeKey, 'settings/social', '二维码')
                    ->helperText($helperText),
            ])
            ->columns(1);
    }

    public function save(LearningPlatformSettingsService $learningPlatformSettings): void
    {
        $state = $this->form->getState();

        Setting::set('site_name', $state['site_name'] ?? '');
        Setting::set('site_description', $state['site_description'] ?? '');
        Setting::set('site_logo', $state['site_logo'] ?? '');
        Setting::set('contact_email', $state['contact_email'] ?? '');
        Setting::set('contact_phone', $state['contact_phone'] ?? '');
        Setting::set('contact_address', $state['contact_address'] ?? '');
        Setting::set('footer_disclaimer', $state['footer_disclaimer'] ?? null);
        Setting::set('footer_copyright', $state['footer_copyright'] ?? null);
        Setting::set('social_linkedin', $this->normalizeSocialUrl($state['social_linkedin'] ?? ''));
        Setting::set('social_linkedin_enabled', (bool) ($state['social_linkedin_enabled'] ?? false));
        Setting::set('social_douyin', $this->normalizeSocialUrl($state['social_douyin'] ?? ''));
        Setting::set('social_douyin_enabled', (bool) ($state['social_douyin_enabled'] ?? false));
        Setting::set('social_xiaohongshu', $this->normalizeSocialUrl($state['social_xiaohongshu'] ?? ''));
        Setting::set('social_xiaohongshu_enabled', (bool) ($state['social_xiaohongshu_enabled'] ?? false));
        Setting::set('social_wechat_channels_qrcode', $this->normalizeUploadedPath($state['social_wechat_channels_qrcode'] ?? ''));
        Setting::set('social_wechat_channels_enabled', (bool) ($state['social_wechat_channels_enabled'] ?? false));
        Setting::set('social_wechat_qrcode', $this->normalizeUploadedPath($state['social_wechat_qrcode'] ?? ''));
        Setting::set('social_wechat_enabled', (bool) ($state['social_wechat_enabled'] ?? false));
        Setting::set('social_facebook', $this->normalizeSocialUrl($state['social_facebook'] ?? ''));
        Setting::set('social_facebook_enabled', (bool) ($state['social_facebook_enabled'] ?? false));
        Setting::set('social_twitter', $this->normalizeSocialUrl($state['social_twitter'] ?? ''));
        Setting::set('social_twitter_enabled', (bool) ($state['social_twitter_enabled'] ?? false));
        Setting::set('social_instagram', $this->normalizeSocialUrl($state['social_instagram'] ?? ''));
        Setting::set('social_instagram_enabled', (bool) ($state['social_instagram_enabled'] ?? false));
        Setting::set('social_youtube', $this->normalizeSocialUrl($state['social_youtube'] ?? ''));
        Setting::set('social_youtube_enabled', (bool) ($state['social_youtube_enabled'] ?? false));
        Setting::set('seo_title', $state['seo_title'] ?? '');
        Setting::set('seo_description', $state['seo_description'] ?? '');
        Setting::set('seo_keywords', $state['seo_keywords'] ?? '');
        Setting::set('maintenance_mode', (bool) ($state['maintenance_mode'] ?? false));
        Setting::set('events_cpd_registration_url', $this->normalizeSocialUrl($state['events_cpd_registration_url'] ?? ''));
        $learningPlatformSettings->savePlatformAConfig($state);

        Notification::make()
            ->title('修改成功')
            ->body('系统设置已保存。')
            ->success()
            ->send();
    }

    private function normalizeBoolean(mixed $value, bool $default = false): bool
    {
        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
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

    private function normalizeSocialUrl(mixed $value): string
    {
        $url = trim((string) $value);

        if ($url === '') {
            return '';
        }

        if (preg_match('#^[a-z][a-z0-9+\-.]*://#i', $url)) {
            return $url;
        }

        return 'https://'.ltrim($url, '/');
    }

    private function normalizeUploadedPath(mixed $value): string
    {
        if (is_array($value)) {
            $value = Arr::first($value);
        }

        return trim((string) ($value ?? ''));
    }
}
