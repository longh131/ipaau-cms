<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\ArticleSummary;
use Illuminate\Console\Command;

class GenerateArticleSummariesCommand extends Command
{
    protected $signature = 'articles:generate-summaries
                            {--category=* : 栏目 ID，可多次指定}
                            {--max-length=100 : 摘要最大字数}
                            {--force : 覆盖已有摘要}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '从正文自动提取文章摘要';

    public function handle(): int
    {
        $categoryIds = collect($this->option('category'))
            ->flatMap(fn (mixed $value): array => explode(',', (string) $value))
            ->map(fn (mixed $value): int => (int) trim((string) $value))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($categoryIds === []) {
            $this->error('请至少指定一个栏目 ID，例如：--category=97 --category=98');

            return self::FAILURE;
        }

        $maxLength = max(1, (int) $this->option('max-length'));
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');

        $query = Article::query()
            ->whereIn('category_id', $categoryIds)
            ->orderBy('id');

        if (! $force) {
            $query->where(function ($builder): void {
                $builder->whereNull('summary')->orWhere('summary', '');
            });
        }

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('没有需要生成摘要的文章。');

            return self::SUCCESS;
        }

        $updated = 0;
        $skipped = 0;
        $tooLong = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(100, function ($articles) use (
            $maxLength,
            $dryRun,
            &$updated,
            &$skipped,
            &$tooLong,
            $bar,
        ): void {
            foreach ($articles as $article) {
                $bar->advance();

                $summary = ArticleSummary::fromContent(
                    $article->content,
                    $article->title,
                    $maxLength,
                );

                if ($summary === '') {
                    $skipped++;

                    continue;
                }

                if (mb_strlen($summary) > $maxLength) {
                    $tooLong++;
                }

                if ($dryRun) {
                    $updated++;

                    continue;
                }

                $article->summary = $summary;
                $article->save();
                $updated++;
            }
        });

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("预览完成：可生成 {$updated} 条，跳过 {$skipped} 条（无可用正文）。");
        } else {
            $this->info("摘要生成完成：更新 {$updated} 条，跳过 {$skipped} 条（无可用正文）。");
        }

        if ($tooLong > 0) {
            $this->warn("有 {$tooLong} 条摘要长度校验异常，请检查 ArticleSummary 逻辑。");
        }

        foreach ($categoryIds as $categoryId) {
            $filled = Article::query()
                ->where('category_id', $categoryId)
                ->whereNotNull('summary')
                ->where('summary', '!=', '')
                ->count();
            $count = Article::query()->where('category_id', $categoryId)->count();
            $this->line("栏目 {$categoryId}：{$filled}/{$count} 条已有摘要");
        }

        return self::SUCCESS;
    }
}
