<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\ArticleCoverImageSelector;
use App\Support\ArticleSortOrder;
use App\Support\IpaMembershipArticlePageScraper;
use App\Support\LegacyNewsImageUrlRewriter;
use App\Support\RichContent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportIpaMembershipArticlesCommand extends Command
{
    protected $signature = 'articles:import-ipa-membership
                            {--section=both : pointofview|interview|both}
                            {--pointofview-category=81 : 会员分享栏目 ID}
                            {--interview-category=80 : 会员专访栏目 ID}
                            {--pointofview-url=https://www.ipaau.org.cn/membership/pointofview/ : 会员分享列表 URL}
                            {--interview-url=https://www.ipaau.org.cn/membership/interview/ : 会员专访列表 URL}
                            {--limit=0 : 每个栏目最多导入条数，0 表示全部}
                            {--sleep=300 : 每条详情页抓取间隔（毫秒）}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '从 ipaau.org.cn 采集会员分享/会员专访文章并导入 CMS';

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
        $section = strtolower(trim((string) $this->option('section')));
        $sections = match ($section) {
            'both' => ['pointofview', 'interview'],
            'pointofview', 'interview' => [$section],
            default => null,
        };

        if ($sections === null) {
            $this->error('section 只能是 pointofview、interview 或 both。');

            return self::FAILURE;
        }

        foreach ($sections as $sectionKey) {
            $this->importSection($sectionKey);
        }

        $this->newLine();
        $this->info('新增 '.$this->stats['created'].' 条，更新 '.$this->stats['updated'].' 条。');
        $this->info('跳过 '.$this->stats['skipped'].' 条，失败 '.$this->stats['failed'].' 条。');
        $this->info('封面 '.$this->stats['covers'].' 张，图片链接替换 '.$this->stats['image_replacements'].' 处。');

        if (! $this->option('dry-run')) {
            $this->info('请将图片文件拷贝到 public/assets/img/ipa-news-legacy/');
        }

        return $this->stats['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function importSection(string $section): void
    {
        $categoryId = (int) $this->option($section === 'pointofview' ? 'pointofview-category' : 'interview-category');
        $listUrl = (string) $this->option($section === 'pointofview' ? 'pointofview-url' : 'interview-url');
        $slugPrefix = $section;
        $limit = max(0, (int) $this->option('limit'));

        $this->newLine();
        $this->info("开始采集 {$section}（栏目 ID {$categoryId}）…");

        $listItems = $this->fetchAllListItems($listUrl);

        if ($listItems === []) {
            $this->warn("{$section} 列表为空，跳过。");

            return;
        }

        if ($limit > 0) {
            $listItems = array_slice($listItems, 0, $limit);
        }

        $this->info('共 '.count($listItems).' 篇文章待导入。');

        $bar = $this->output->createProgressBar(count($listItems));
        $bar->start();

        foreach ($listItems as $item) {
            $bar->advance();

            try {
                $this->importArticle($categoryId, $slugPrefix, $item);
            } catch (\Throwable $exception) {
                $this->stats['failed']++;
                $this->newLine();
                $this->warn("导入失败 [{$item['legacy_id']}] {$item['title']}：".$exception->getMessage());
            }

            usleep(max(0, (int) $this->option('sleep')) * 1000);
        }

        $bar->finish();
        $this->newLine();

        if (! $this->option('dry-run')) {
            $remapped = ArticleSortOrder::mirrorImportOrderForCategory($categoryId);
            $this->info("栏目 ID {$categoryId} 当前文章总数：".Article::query()->where('category_id', $categoryId)->count());
            $this->info("已按全站规则校正 sort_order：{$remapped} 篇。");
        }
    }

    /**
     * @return array<int, array{legacy_id: string, url: string, title: string, published_at: ?\Carbon\Carbon}>
     */
    private function fetchAllListItems(string $listUrl): array
    {
        $firstPageHtml = $this->fetchHtml($listUrl);

        if ($firstPageHtml === null) {
            return [];
        }

        $maxPage = IpaMembershipArticlePageScraper::scrapeMaxPageNumber($firstPageHtml);
        $items = IpaMembershipArticlePageScraper::scrapeListItems($firstPageHtml);
        $seen = [];

        foreach ($items as $item) {
            $seen[$item['legacy_id']] = true;
        }

        for ($page = 2; $page <= $maxPage; $page++) {
            $pageUrl = $this->buildPageUrl($listUrl, $page);
            $html = $this->fetchHtml($pageUrl);

            if ($html === null) {
                continue;
            }

            foreach (IpaMembershipArticlePageScraper::scrapeListItems($html) as $item) {
                if (isset($seen[$item['legacy_id']])) {
                    continue;
                }

                $seen[$item['legacy_id']] = true;
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param  array{legacy_id: string, url: string, title: string, published_at: ?\Carbon\Carbon}  $item
     */
    private function importArticle(int $categoryId, string $slugPrefix, array $item): void
    {
        $detailHtml = $this->fetchHtml($item['url']);

        if ($detailHtml === null) {
            throw new \RuntimeException('详情页抓取失败');
        }

        $detail = IpaMembershipArticlePageScraper::scrapeDetail($detailHtml);
        $title = $item['title'] !== '' ? $item['title'] : ($detail['title'] ?? '');
        $content = $detail['content_html'];
        $publishedAt = $item['published_at'] ?? $detail['published_at'];

        if ($title === '' || $content === '') {
            $this->stats['skipped']++;

            return;
        }

        $rewriteResult = LegacyNewsImageUrlRewriter::rewriteContent($content);
        $content = $rewriteResult['content'];
        $this->stats['image_replacements'] += $rewriteResult['replacements'];

        $firstImageUrl = ArticleCoverImageSelector::selectFirstImageUrl($content);
        $coverImage = null;

        if ($firstImageUrl !== null) {
            $coverPath = LegacyNewsImageUrlRewriter::rewriteUrl($firstImageUrl) ?? $firstImageUrl;
            $coverImage = $this->resolveCoverImage($coverPath);
        }

        if ($coverImage !== null) {
            $this->stats['covers']++;
        }

        $summary = $this->makeSummary($content);
        $slug = $this->makeSlug($slugPrefix, $item['legacy_id'], $title);

        $payload = [
            'title' => $title,
            'category_id' => $categoryId,
            'content' => RichContent::encodeDocumentForForm($content) ?? $content,
            'summary' => $summary,
            'cover_image' => $coverImage,
            'author' => null,
            'source' => null,
            'view_count' => 0,
            'published_at' => $publishedAt,
            'is_active' => true,
            'is_featured' => false,
            'is_sticky' => false,
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

    private function buildPageUrl(string $listUrl, int $page): string
    {
        $listUrl = rtrim($listUrl, '/');

        if (preg_match('/([?&])page=\d+/i', $listUrl)) {
            return preg_replace('/([?&])page=\d+/i', '${1}page='.$page, $listUrl) ?? $listUrl;
        }

        if (str_contains($listUrl, '?')) {
            return $listUrl.'&page='.$page;
        }

        return $listUrl.'/?page='.$page;
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

    private function makeSummary(string $html): ?string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($html)) ?? '');

        if ($text === '') {
            return null;
        }

        return Str::limit($text, 220);
    }
}
