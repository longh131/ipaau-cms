<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Support\ArticleCoverImageSelector;
use App\Support\ArticleSortOrder;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use App\Support\CategoryListTemplate\MemberSpotlightTemplate;
use App\Support\IpaMemberExcellentPageScraper;
use App\Support\IpaMembershipArticlePageScraper;
use App\Support\LegacyNewsImageUrlRewriter;
use App\Support\RichContent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportIpaMemberExcellentCommand extends Command
{
    protected $signature = 'articles:import-ipa-member-excellent
                            {--url=https://www.ipaau.org.cn/membership/excellent/ : 会员风采页面 URL}
                            {--category=79 : 目标栏目 ID}
                            {--slug-prefix=excellent : 别名前缀}
                            {--limit=0 : 最多导入条数，0 表示全部}
                            {--sleep=200 : 每条详情页抓取间隔（毫秒）}
                            {--configure-category : 同步栏目列表模板与扩展字段}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '从 ipaau.org.cn/membership/excellent 采集会员风采并导入 CMS';

    /** @var array<string, int> */
    private array $stats = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'failed' => 0,
        'covers' => 0,
        'image_replacements' => 0,
    ];

    public function handle(): int
    {
        $categoryId = (int) $this->option('category');
        $slugPrefix = trim((string) $this->option('slug-prefix'), '-');
        $limit = max(0, (int) $this->option('limit'));

        if ($slugPrefix === '') {
            $this->error('slug-prefix 不能为空。');

            return self::FAILURE;
        }

        if ($this->option('configure-category') && ! $this->option('dry-run')) {
            $this->configureCategory($categoryId);
        }

        $html = $this->fetchHtml((string) $this->option('url'));

        if ($html === null) {
            $this->error('无法抓取会员风采页面。');

            return self::FAILURE;
        }

        $members = IpaMemberExcellentPageScraper::scrapeMembers($html);
        $this->info('解析到 '.count($members).' 位会员。');

        if ($limit > 0) {
            $members = array_slice($members, 0, $limit);
        }

        $bar = $this->output->createProgressBar(count($members));
        $bar->start();

        foreach ($members as $member) {
            $bar->advance();

            try {
                $this->importMember($categoryId, $slugPrefix, $member);
            } catch (\Throwable $exception) {
                $this->stats['failed']++;
                $this->newLine();
                $this->warn("导入失败 [{$member['title']}]：".$exception->getMessage());
            }

            usleep(max(0, (int) $this->option('sleep')) * 1000);
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('新增 '.$this->stats['created'].' 条，更新 '.$this->stats['updated'].' 条。');
        $this->info('跳过 '.$this->stats['skipped'].' 条，失败 '.$this->stats['failed'].' 条。');
        $this->info('封面 '.$this->stats['covers'].' 张，图片链接替换 '.$this->stats['image_replacements'].' 处。');

        if (! $this->option('dry-run')) {
            $remapped = ArticleSortOrder::mirrorImportOrderForCategory($categoryId);
            $this->info("已按全站规则校正 sort_order：{$remapped} 篇。");
            $this->info('栏目 ID '.$categoryId.' 当前文章总数：'.Article::query()->where('category_id', $categoryId)->count());
            $this->info('请将图片文件拷贝到 public/assets/img/ipa-news-legacy/');
        }

        return $this->stats['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  array{
     *     title: string,
     *     position: ?string,
     *     summary: ?string,
     *     image_url: ?string,
     *     detail_url: ?string,
     *     detail_legacy_id: ?string
     * }  $member
     */
    private function importMember(int $categoryId, string $slugPrefix, array $member): void
    {
        $title = trim($member['title']);

        if ($title === '') {
            $this->stats['skipped']++;

            return;
        }

        $content = '';

        if (filled($member['detail_url'])) {
            $detailHtml = $this->fetchHtml($member['detail_url']);

            if ($detailHtml !== null) {
                $detail = IpaMembershipArticlePageScraper::scrapeDetail($detailHtml);
                $content = $detail['content_html'];
            }
        }

        if ($content === '' && filled($member['summary'])) {
            $content = '<p>'.e($member['summary']).'</p>';
        }

        if ($content === '') {
            $this->stats['skipped']++;

            return;
        }

        $rewriteResult = LegacyNewsImageUrlRewriter::rewriteContent($content);
        $content = $rewriteResult['content'];
        $this->stats['image_replacements'] += $rewriteResult['replacements'];

        $coverImage = null;

        if (filled($member['image_url'])) {
            $coverPath = LegacyNewsImageUrlRewriter::rewriteUrl($member['image_url']) ?? $member['image_url'];
            $coverImage = $this->resolveCoverImage($coverPath);
        }

        if ($coverImage === null) {
            $firstImageUrl = ArticleCoverImageSelector::selectFirstImageUrl($content);

            if ($firstImageUrl !== null) {
                $coverPath = LegacyNewsImageUrlRewriter::rewriteUrl($firstImageUrl) ?? $firstImageUrl;
                $coverImage = $this->resolveCoverImage($coverPath);
            }
        }

        if ($coverImage !== null) {
            $this->stats['covers']++;
        }

        $legacyId = $member['detail_legacy_id'] ?? substr(md5($title), 0, 12);
        $slug = $this->makeSlug($slugPrefix, (string) $legacyId, $title);

        $extraFields = [];

        if (filled($member['position'])) {
            $extraFields[MemberSpotlightTemplate::POSITION_KEY] = $member['position'];
        }

        $payload = [
            'title' => $title,
            'category_id' => $categoryId,
            'content' => RichContent::encodeDocumentForForm($content) ?? $content,
            'summary' => $member['summary'],
            'cover_image' => $coverImage,
            'author' => null,
            'source' => null,
            'view_count' => 0,
            'published_at' => null,
            'is_active' => true,
            'is_featured' => false,
            'is_sticky' => false,
            'extra_fields' => $extraFields,
        ];

        if ($this->option('dry-run')) {
            $this->stats['created']++;

            return;
        }

        $article = Article::withTrashed()->where('slug', $slug)->first();

        if ($article !== null) {
            if ($article->trashed()) {
                $article->restore();
            }

            $article->fill($payload);
            $article->save();
            ArticleSortOrder::syncToId($article);
            $this->stats['updated']++;
        } else {
            $article = Article::query()->create(array_merge($payload, [
                'slug' => $slug,
                'sort_order' => 0,
            ]));
            ArticleSortOrder::syncToId($article);
            $this->stats['created']++;
        }
    }

    private function configureCategory(int $categoryId): void
    {
        $category = Category::query()->find($categoryId);

        if ($category === null) {
            $this->warn("栏目 ID {$categoryId} 不存在，跳过配置。");

            return;
        }

        $category->list_template = CategoryListTemplateRegistry::TEMPLATE_MEMBER_SPOTLIGHT;
        $category->article_extra_field_schema = [
            MemberSpotlightTemplate::RECOMMENDED_EXTRA_FIELD,
        ];
        $category->save();

        $this->info("已配置栏目 {$categoryId}：列表模板=会员风采，扩展字段 position。");
    }

    private function fetchHtml(string $url): ?string
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::timeout(120)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; IPA-CMS-Importer/1.0)'])
                    ->get($url);

                if ($response->successful()) {
                    return $response->body();
                }
            } catch (\Throwable) {
                // retry
            }

            usleep(500000);
        }

        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: Mozilla/5.0 (compatible; IPA-CMS-Importer/1.0)\r\n",
                'timeout' => 120,
            ],
        ]);

        $body = @file_get_contents($url, false, $context);

        return is_string($body) && $body !== '' ? $body : null;
    }

    private function resolveCoverImage(?string $coverPath): ?string
    {
        $stored = LegacyNewsImageUrlRewriter::toStoredCoverPath($coverPath);

        if ($stored === null || strlen($stored) > 255) {
            return null;
        }

        return $stored;
    }

    private function makeSlug(string $prefix, string $legacyId, string $title): string
    {
        if ($legacyId !== '') {
            return $prefix.'-'.$legacyId;
        }

        $base = Str::slug($title);

        if ($base === '') {
            $base = 'article-'.substr(md5($title), 0, 12);
        }

        return $prefix.'-'.$base;
    }
}
