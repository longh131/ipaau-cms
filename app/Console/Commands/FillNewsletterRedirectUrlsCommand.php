<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\NewsletterCatalog;
use App\Support\NewsletterHtml;
use Illuminate\Console\Command;

class FillNewsletterRedirectUrlsCommand extends Command
{
    protected $signature = 'articles:fill-newsletter-redirects
                            {--index=bak/index.aspx : 列表页路径（相对项目根目录）}
                            {--newsletter-dir=bak/newsLetter : 用于解析实际文件路径的源目录}
                            {--public-dir=assets/newsletter : public 下资源目录}
                            {--category=96 : 目标栏目 ID}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '为电子通讯文章补充跳转到 /assets/newsletter/ 下对应文件的链接';

    public function handle(): int
    {
        $indexPath = base_path($this->option('index'));
        $newsletterRoot = base_path($this->option('newsletter-dir'));
        $publicDir = trim(str_replace('\\', '/', (string) $this->option('public-dir')), '/');
        $categoryId = (int) $this->option('category');
        $dryRun = (bool) $this->option('dry-run');

        if (! is_file($indexPath)) {
            $this->error("列表页不存在：{$indexPath}");

            return self::FAILURE;
        }

        $entries = NewsletterCatalog::parse($indexPath);
        $updated = 0;
        $skipped = 0;
        $missingArticle = 0;

        $bar = $this->output->createProgressBar(count($entries));
        $bar->start();

        foreach ($entries as $entry) {
            $bar->advance();

            $article = Article::query()
                ->where('category_id', $categoryId)
                ->where('title', $entry['title'])
                ->first();

            if ($article === null) {
                $missingArticle++;
                $skipped++;

                continue;
            }

            $localFile = NewsletterHtml::resolveLocalFile($newsletterRoot, $entry['relative_path']);

            if ($localFile === null) {
                $publicRelative = $entry['relative_path'];
            } else {
                $publicRelative = ltrim(str_replace('\\', '/', substr(
                    $localFile,
                    strlen(rtrim($newsletterRoot, '/\\')) + 1
                )), '/');
            }

            $publicPath = public_path($publicDir.'/'.$publicRelative);

            if (! is_file($publicPath)) {
                $skipped++;
                $this->newLine();
                $this->warn("跳过（public 文件不存在）：{$entry['title']} → {$publicDir}/{$publicRelative}");

                continue;
            }

            $redirectUrl = url(NewsletterHtml::publicAssetUrl($publicDir, $publicRelative));

            if ($dryRun) {
                $updated++;

                continue;
            }

            $article->redirect_url = $redirectUrl;
            $article->save();
            $updated++;
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("预览完成：可更新 {$updated} 条，跳过 {$skipped} 条（含未匹配文章 {$missingArticle} 条）。");
        } else {
            $this->info("更新完成：已设置跳转链接 {$updated} 条，跳过 {$skipped} 条。");
            $filled = Article::query()
                ->where('category_id', $categoryId)
                ->whereNotNull('redirect_url')
                ->where('redirect_url', '!=', '')
                ->count();
            $this->info("栏目 ID {$categoryId} 当前有跳转链接的文章：{$filled} 条。");
        }

        return self::SUCCESS;
    }
}
