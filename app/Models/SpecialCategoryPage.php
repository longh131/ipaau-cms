<?php

namespace App\Models;

use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialCategoryPage extends Model
{
    public const FEATURE_COURSE_LIST = 'course_list';

    public const FEATURE_CERTIFICATE_LOOKUP = 'certificate_lookup';

    public const FEATURE_VIDEO_HUB = 'video_hub';

    public const FEATURE_CPD_RECORDS = 'cpd_records';

    public const CPD_RECORDS_BUTTON_LABEL = '查询学分证明';

    public const CPD_RECORDS_BUTTON_URL = 'http://crm.ipaau.org.cn/member/cpe/ser.aspx';

    /** @var array<string, string> */
    public const FEATURE_TYPE_OPTIONS = [
        self::FEATURE_COURSE_LIST => '课程汇总',
        self::FEATURE_CERTIFICATE_LOOKUP => '证书查询',
        self::FEATURE_VIDEO_HUB => 'IPA 视频汇总',
        self::FEATURE_CPD_RECORDS => '我的 CPD 记录',
    ];

    protected $fillable = [
        'category_id',
        'feature_type',
        'body_html_top',
        'body_html_bottom',
        'course_category_ids',
        'certificate_title',
        'certificate_summary',
    ];

    protected $casts = [
        'course_category_ids' => 'array',
    ];

    protected $attributes = [
        'feature_type' => self::FEATURE_COURSE_LIST,
    ];

    public function listTemplate(): string
    {
        return match ($this->feature_type) {
            self::FEATURE_CERTIFICATE_LOOKUP => CategoryListTemplateRegistry::TEMPLATE_SPECIAL_CERTIFICATE_LOOKUP,
            self::FEATURE_VIDEO_HUB => CategoryListTemplateRegistry::TEMPLATE_SPECIAL_VIDEO_HUB,
            self::FEATURE_CPD_RECORDS => CategoryListTemplateRegistry::TEMPLATE_SPECIAL_CPD_RECORDS,
            default => CategoryListTemplateRegistry::TEMPLATE_SPECIAL_COURSE_LIST,
        };
    }

    public function certificateTitleForFrontend(): string
    {
        $title = trim((string) ($this->certificate_title ?? ''));

        return $title !== '' ? $title : '证书查询';
    }

    public function certificateSummaryForFrontend(): string
    {
        return trim((string) ($this->certificate_summary ?? ''));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return array<int>
     */
    public function resolvedCourseCategoryIds(): array
    {
        $ids = $this->course_category_ids;

        if (! is_array($ids) || $ids === []) {
            return Course::categoryIds();
        }

        $normalized = array_values(array_unique(array_map(
            static fn (mixed $id): int => (int) $id,
            $ids,
        )));

        $allowed = array_values(array_intersect($normalized, Course::categoryIds()));

        return $allowed !== [] ? $allowed : Course::categoryIds();
    }

    public function bodyHtmlTopForFrontend(): string
    {
        return trim((string) ($this->body_html_top ?? ''));
    }

    public function bodyHtmlBottomForFrontend(): string
    {
        return trim((string) ($this->body_html_bottom ?? ''));
    }

    public function cpdRecordsButtonLabelForFrontend(): string
    {
        return self::CPD_RECORDS_BUTTON_LABEL;
    }

    public function cpdRecordsButtonUrlForFrontend(): string
    {
        return self::CPD_RECORDS_BUTTON_URL;
    }
}
