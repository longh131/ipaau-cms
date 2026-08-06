<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\ArticleCoverImageSelector;
use App\Support\RichContent;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportIpaNewsArticlesCommand extends Command
{
    protected $signature = 'articles:import-ipa-news
                            {--file=bak/IPA_news.xlsx : Excel 文件路径（相对项目根目录）}
                            {--category=31 : 目标栏目 ID}
                            {--slug-prefix=news : 别名前缀}
                            {--dry-run : 仅预览，不写入数据库}
                            {--skip-download : 跳过封面图下载}';

    protected $description = '从 IPA_news.xlsx 导入新闻到指定栏目';

    public function handle(): int
    {
        $file = base_path($this->option('file'));
        $categoryId = (int) $this->option('category');
        $slugPrefix = trim((string) $this->option('slug-prefix'), '-');
        $dryRun = (bool) $this->option('dry-run');
        $skipDownload = (bool) $this->option('skip-download') || $dryRun;

        if ($slugPrefix === '') {
            $this->error('slug-prefix 不能为空。');

            return self::FAILURE;
        }

        if (! is_file($file)) {
            $this->error("文件不存在：{$file}");

            return self::FAILURE;
        }

        $sheet = IOFactory::load($file)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        $headerRow = array_shift($rows);

        if ($headerRow === null) {
            $this->error('Excel 文件为空。');

            return self::FAILURE;
        }

        $columnMap = $this->buildColumnMap($headerRow);
        $required = ['id', 'title', 'content', 'addtime'];

        foreach ($required as $field) {
            if (! isset($columnMap[$field])) {
                $this->error("缺少必需列：{$field}");

                return self::FAILURE;
            }
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $skippedBrackets = 0;
        $covers = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $bar->advance();

            $title = trim((string) ($row[$columnMap['title']] ?? ''));

            if ($title === '') {
                $skipped++;

                continue;
            }

            if (str_contains($title, '【') || str_contains($title, '】')) {
                $skippedBrackets++;

                continue;
            }

            $legacyId = trim((string) ($row[$columnMap['id']] ?? ''));
            $rawContent = $this->normalizeContent((string) ($row[$columnMap['content']] ?? ''));
            $slug = $this->makeSlug($slugPrefix, $legacyId, $title);
            $publishedAt = $this->parsePublishedAt($row[$columnMap['addtime']] ?? null);
            $author = isset($columnMap['zuozhe'])
                ? $this->nullableString($row[$columnMap['zuozhe']] ?? null)
                : null;
            $source = isset($columnMap['ttfrom'])
                ? $this->nullableString($row[$columnMap['ttfrom']] ?? null)
                : null;
            $viewCount = isset($columnMap['readcount'])
                ? max(0, (int) ($row[$columnMap['readcount']] ?? 0))
                : 0;
            $isSticky = isset($columnMap['settop'])
                && (int) ($row[$columnMap['settop']] ?? 0) === 1;

            $coverImage = null;

            if (! $skipDownload) {
                $coverUrl = ArticleCoverImageSelector::selectCoverUrl($rawContent);

                if ($coverUrl !== null) {
                    $coverImage = ArticleCoverImageSelector::downloadCover($coverUrl);

                    if ($coverImage !== null) {
                        $covers++;
                    }
                }
            }

            $summary = $this->makeSummary($rawContent);

            $payload = [
                'title' => $title,
                'category_id' => $categoryId,
                'content' => RichContent::encodeDocumentForForm($rawContent) ?? $rawContent,
                'summary' => $summary,
                'cover_image' => $coverImage,
                'author' => $author,
                'source' => $source,
                'view_count' => $viewCount,
                'published_at' => $publishedAt,
                'is_active' => true,
                'is_featured' => false,
                'is_sticky' => $isSticky,
            ];

            if ($dryRun) {
                $created++;

                continue;
            }

            $article = Article::withTrashed()->where('slug', $slug)->first();

            if ($article !== null) {
                if ($article->trashed()) {
                    $article->restore();
                }

                $article->fill($payload);
                $article->sort_order = $article->id;
                $article->save();
                $updated++;
            } else {
                $article = Article::query()->create(array_merge($payload, [
                    'slug' => $slug,
                    'sort_order' => 0,
                ]));
                $article->sort_order = $article->id;
                $article->save();
                $created++;
            }
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("预览完成：可导入 {$created} 条，跳过空标题 {$skipped} 条，跳过含【】标题 {$skippedBrackets} 条。");
        } else {
            $this->info("导入完成：新增 {$created} 条，更新 {$updated} 条，跳过空标题 {$skipped} 条，跳过含【】标题 {$skippedBrackets} 条，封面 {$covers} 张。");
            $this->info('栏目 ID '.$categoryId.' 当前文章总数：'.Article::query()->where('category_id', $categoryId)->count());
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string|int, mixed>  $headerRow
     * @return array<string, string|int>
     */
    private function buildColumnMap(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $column => $header) {
            $key = strtolower(trim((string) $header));

            if ($key !== '') {
                $map[$key] = $column;
            }
        }

        return $map;
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

        return $base;
    }

    private function normalizeContent(string $content): string
    {
        $content = str_replace(['_x000d_', '_x000D_'], '', $content);
        $content = preg_replace('/\s*(?:<br\s*\/?>\s*){3,}/i', '<br><br>', $content) ?? $content;

        return trim($content);
    }

    private function makeSummary(string $html): ?string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($html)) ?? '');

        if ($text === '') {
            return null;
        }

        return Str::limit($text, 220);
    }

    private function parsePublishedAt(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->startOfDay();
        }

        $string = trim((string) $value);

        try {
            return Carbon::parse($string)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $string = trim((string) ($value ?? ''));

        return $string === '' ? null : $string;
    }
}
