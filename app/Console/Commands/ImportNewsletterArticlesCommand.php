<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\ArticleSlug;
use App\Support\ArticleSummary;
use App\Support\NewsletterCatalog;
use App\Support\NewsletterHtml;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportNewsletterArticlesCommand extends Command
{
    protected $signature = 'articles:import-newsletter
                            {--index=bak/index.aspx : 列表页路径（相对项目根目录）}
                            {--newsletter-dir=bak/newsLetter : 电子通讯源目录}
                            {--public-dir=assets/newsletter : 发布到 public 下的目录}
                            {--category=96 : 目标栏目 ID}
                            {--cover=assets/img/news/cover.jpg : 统一封页路径}
                            {--skip-sync : 不同步资源到 public}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '从 index.aspx 与 newsLetter 目录导入中国区电子通讯文章';

    public function handle(): int
    {
        $indexPath = base_path($this->option('index'));
        $newsletterRoot = base_path($this->option('newsletter-dir'));
        $publicDir = trim(str_replace('\\', '/', (string) $this->option('public-dir')), '/');
        $categoryId = (int) $this->option('category');
        $coverImage = trim(str_replace('\\', '/', (string) $this->option('cover')), '/');
        $dryRun = (bool) $this->option('dry-run');

        if (! is_file($indexPath)) {
            $this->error("列表页不存在：{$indexPath}");

            return self::FAILURE;
        }

        if (! is_dir($newsletterRoot)) {
            $this->error("源目录不存在：{$newsletterRoot}");

            return self::FAILURE;
        }

        if (! $dryRun && ! $this->option('skip-sync')) {
            $this->syncAssets($newsletterRoot, public_path($publicDir));
        }

        $entries = NewsletterCatalog::parse($indexPath);
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $htmlImported = 0;
        $fileImported = 0;
        $placeholderImported = 0;

        $bar = $this->output->createProgressBar(count($entries));
        $bar->start();

        foreach ($entries as $entry) {
            $bar->advance();

            $localFile = NewsletterHtml::resolveLocalFile($newsletterRoot, $entry['relative_path']);
            $slug = ArticleSlug::ensureUnique(
                NewsletterCatalog::makeSlug($entry['relative_path'], $entry['issue_number']),
            );

            $content = null;
            $redirectUrl = null;

            if ($localFile === null) {
                $content = '<p>该期电子通讯源文件暂未归档，标题信息已保留。</p>';
                $placeholderImported++;
            } elseif ($entry['extension'] === 'html') {
                $rawHtml = NewsletterHtml::readHtmlFile($localFile);
                $content = NewsletterHtml::rewriteAssetUrls($rawHtml, $entry['asset_dir']);
                $htmlImported++;
            } else {
                $relativeAsset = ltrim(str_replace('\\', '/', substr($localFile, strlen(rtrim($newsletterRoot, '/\\')) + 1)), '/');
                $redirectUrl = url(NewsletterHtml::publicAssetUrl($publicDir, $relativeAsset));
                $content = '<p><a href="'.e($redirectUrl).'" target="_blank" rel="noopener">点击查看或下载该期电子通讯（'.strtoupper($entry['extension']).'）</a></p>';
                $fileImported++;
            }

            $summary = ArticleSummary::fromContent($content, $entry['title']);

            $payload = [
                'title' => $entry['title'],
                'category_id' => $categoryId,
                'content' => $content,
                'summary' => $summary !== '' ? $summary : ArticleSummary::fromContent($entry['title'], null, 100),
                'cover_image' => $coverImage,
                'redirect_url' => $redirectUrl,
                'published_at' => $entry['published_at'],
                'sort_order' => $entry['issue_number'] !== null ? (10000 - $entry['issue_number']) : 0,
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
            $this->info("预览完成：可导入 {$created} 条。");
        } else {
            $this->info("导入完成：新增 {$created} 条，更新 {$updated} 条，跳过 {$skipped} 条。");
            $this->info("HTML 正文 {$htmlImported} 条，PDF/RAR 跳转 {$fileImported} 条，占位 {$placeholderImported} 条。");
            $this->info('栏目 ID '.$categoryId.' 当前文章总数：'.Article::query()->where('category_id', $categoryId)->count());
        }

        return self::SUCCESS;
    }

    private function syncAssets(string $source, string $destination): void
    {
        if (is_dir($destination)) {
            $this->line('资源目录已存在，增量同步：'.$destination);

            File::copyDirectory($source, $destination);

            return;
        }

        $this->line('复制资源到：'.$destination);
        File::copyDirectory($source, $destination);
    }
}
