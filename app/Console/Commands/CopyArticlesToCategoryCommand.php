<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class CopyArticlesToCategoryCommand extends Command
{
    protected $signature = 'articles:copy-to-category
                            {--from=* : 源栏目 ID（可多个）}
                            {--to= : 目标栏目 ID}
                            {--slug-prefix=featured-news : 新文章别名前缀}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '将多个源栏目的文章按发布时间合并复制到目标栏目';

    public function handle(): int
    {
        $fromIds = array_map('intval', $this->option('from'));
        $toId = (int) $this->option('to');
        $slugPrefix = trim((string) $this->option('slug-prefix'), '-');
        $dryRun = (bool) $this->option('dry-run');

        if ($fromIds === [] || $toId <= 0) {
            $this->error('请指定 --from=97 --from=98 --to=111');

            return self::FAILURE;
        }

        if ($slugPrefix === '') {
            $this->error('slug-prefix 不能为空。');

            return self::FAILURE;
        }

        $articles = Article::query()
            ->whereIn('category_id', $fromIds)
            ->orderByRaw('published_at IS NULL')
            ->orderBy('published_at')
            ->orderBy('id')
            ->get();

        if ($articles->isEmpty()) {
            $this->warn('源栏目下没有文章。');

            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($articles->count());
        $bar->start();

        foreach ($articles as $source) {
            $bar->advance();

            $slug = $this->makeSlug($slugPrefix, $source->id, $source->slug);

            $payload = [
                'title' => $source->title,
                'category_id' => $toId,
                'content' => $source->content,
                'summary' => $source->summary,
                'cover_image' => $source->cover_image,
                'author' => $source->author,
                'source' => $source->source,
                'view_count' => 0,
                'redirect_url' => $source->redirect_url,
                'published_at' => $source->published_at,
                'is_active' => $source->is_active,
                'is_featured' => $source->is_featured,
                'is_sticky' => $source->is_sticky,
                'extra_fields' => $source->extra_fields,
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

                if ((int) $article->category_id !== $toId) {
                    $skipped++;

                    continue;
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
            $this->info("预览完成：可复制 {$created} 篇到栏目 {$toId}。");
        } else {
            $this->info("完成：新增 {$created} 篇，更新 {$updated} 篇，跳过 slug 冲突 {$skipped} 篇。");
            $this->info('目标栏目当前文章总数：'.Article::query()->where('category_id', $toId)->count());
        }

        return self::SUCCESS;
    }

    private function makeSlug(string $prefix, int $sourceId, string $originalSlug): string
    {
        return $prefix.'-'.$sourceId;
    }
}
