<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\LegacyNewsImageUrlRewriter;
use App\Support\RichContent;
use Illuminate\Console\Command;

class RewriteIpaNewsLegacyImagesCommand extends Command
{
    protected $signature = 'articles:rewrite-ipa-news-images
                            {--category=31 : 目标栏目 ID}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '将栏目文章正文中的旧 uploadfile 图片链接改为 /assets/img/ipa-news-legacy/';

    public function handle(): int
    {
        $categoryId = (int) $this->option('category');
        $dryRun = (bool) $this->option('dry-run');

        $articles = Article::query()
            ->where('category_id', $categoryId)
            ->orderBy('id')
            ->get();

        if ($articles->isEmpty()) {
            $this->warn("栏目 {$categoryId} 下没有文章。");

            return self::SUCCESS;
        }

        $updatedArticles = 0;
        $totalReplacements = 0;

        $bar = $this->output->createProgressBar($articles->count());
        $bar->start();

        foreach ($articles as $article) {
            $bar->advance();

            $rawContent = RichContent::toHtml($article->content) ?? (string) $article->content;
            $result = LegacyNewsImageUrlRewriter::rewriteContent($rawContent);

            if (! $result['changed']) {
                continue;
            }

            $updatedArticles++;
            $totalReplacements += $result['replacements'];

            if ($dryRun) {
                continue;
            }

            $article->content = RichContent::encodeDocumentForForm($result['content']) ?? $result['content'];
            $article->save();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("预览完成：{$updatedArticles} 篇文章含可替换图片，共 {$totalReplacements} 处链接。");
        } else {
            $this->info("完成：已更新 {$updatedArticles} 篇文章，共替换 {$totalReplacements} 处图片链接。");
            $this->info('请将图片文件拷贝到 public/assets/img/ipa-news-legacy/');
        }

        return self::SUCCESS;
    }
}
