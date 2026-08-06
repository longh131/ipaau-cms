<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\ArticleCoverImageSelector;
use App\Support\LegacyNewsImageUrlRewriter;
use App\Support\RichContent;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportIpaMediaArticlesCommand extends Command
{
    protected $signature = 'articles:import-ipa-media
                            {--file=bak/IPA_media.xlsx : Excel 文件路径（相对项目根目录）}
                            {--category=70 : 目标栏目 ID}
                            {--slug-prefix=media : 别名前缀}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '从 IPA_media.xlsx 导入「IPA在媒体」文章';

    public function handle(): int
    {
        $file = base_path($this->option('file'));
        $categoryId = (int) $this->option('category');
        $slugPrefix = trim((string) $this->option('slug-prefix'), '-');
        $dryRun = (bool) $this->option('dry-run');

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
        $covers = 0;
        $imageReplacements = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $bar->advance();

            $title = trim((string) ($row[$columnMap['title']] ?? ''));

            if ($title === '') {
                $skipped++;

                continue;
            }

            $legacyId = trim((string) ($row[$columnMap['id']] ?? ''));
            $rawContent = $this->normalizeContent((string) ($row[$columnMap['content']] ?? ''));
            $rewriteResult = LegacyNewsImageUrlRewriter::rewriteContent($rawContent);
            $content = $rewriteResult['content'];
            $imageReplacements += $rewriteResult['replacements'];

            $firstImageUrl = ArticleCoverImageSelector::selectFirstImageUrl($rawContent);
            $coverImage = $firstImageUrl !== null
                ? LegacyNewsImageUrlRewriter::toStoredCoverPath(LegacyNewsImageUrlRewriter::rewriteUrl($firstImageUrl))
                : null;

            if ($coverImage !== null) {
                $covers++;
            }

            $slug = $this->makeSlug($slugPrefix, $legacyId, $title);
            $publishedAt = $this->parsePublishedAt($row[$columnMap['addtime']] ?? null);
            $summary = $this->makeSummary($content);

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
            $this->info("预览完成：可导入 {$created} 条，跳过空标题 {$skipped} 条，封面 {$covers} 张，图片链接替换 {$imageReplacements} 处。");
        } else {
            $this->info("导入完成：新增 {$created} 条，更新 {$updated} 条，跳过空标题 {$skipped} 条，封面 {$covers} 张，图片链接替换 {$imageReplacements} 处。");
            $this->info('栏目 ID '.$categoryId.' 当前文章总数：'.Article::query()->where('category_id', $categoryId)->count());
            $this->info('请将图片文件拷贝到 public/assets/img/ipa-news-legacy/');
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
}
