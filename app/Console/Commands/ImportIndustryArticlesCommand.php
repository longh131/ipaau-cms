<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\RichContent;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportIndustryArticlesCommand extends Command
{
    protected $signature = 'articles:import-industry
                            {--file=bak/IPA_Industry.xlsx : Excel 文件路径（相对项目根目录）}
                            {--category=97 : 目标栏目 ID}
                            {--slug-prefix=industry : 别名前缀，例如 read 会生成 read-123}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '从 Excel 导入文章到指定栏目';

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
        $required = ['title', 'content', 'addtime'];

        foreach ($required as $field) {
            if (! isset($columnMap[$field])) {
                $this->error("缺少必需列：{$field}");

                return self::FAILURE;
            }
        }

        $legacyIdColumn = $columnMap['id'] ?? null;
        $created = 0;
        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $bar->advance();

            $title = trim((string) ($row[$columnMap['title']] ?? ''));

            if ($title === '') {
                $skipped++;

                continue;
            }

            $legacyId = $legacyIdColumn !== null
                ? trim((string) ($row[$legacyIdColumn] ?? ''))
                : '';

            $slug = $this->makeSlug($slugPrefix, $legacyId, $title);
            $content = $this->normalizeContent((string) ($row[$columnMap['content']] ?? ''));
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
            $redirectUrl = isset($columnMap['url'])
                ? $this->nullableString($row[$columnMap['url']] ?? null)
                : null;
            $summary = isset($columnMap['brief'])
                ? $this->nullableString($row[$columnMap['brief']] ?? null)
                : null;

            $payload = [
                'title' => $title,
                'category_id' => $categoryId,
                'content' => RichContent::encodeDocumentForForm($content) ?? $content,
                'summary' => $summary,
                'redirect_url' => $redirectUrl,
                'author' => $author,
                'source' => $source,
                'view_count' => $viewCount,
                'published_at' => $publishedAt,
                'is_active' => true,
                'is_featured' => false,
                'is_sticky' => false,
                'sort_order' => 0,
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
                $article->save();
                $updated++;
            } else {
                Article::query()->create(array_merge($payload, ['slug' => $slug]));
                $created++;
            }
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("预览完成：可导入 {$created} 条，跳过 {$skipped} 条（空标题）。");
        } else {
            $this->info("导入完成：新增 {$created} 条，更新 {$updated} 条，跳过 {$skipped} 条。");
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

        $base = \Illuminate\Support\Str::slug($title);

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
