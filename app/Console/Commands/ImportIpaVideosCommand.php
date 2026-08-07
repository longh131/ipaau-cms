<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\IpaVideoPageScraper;
use App\Support\RichContent;
use App\Support\VideoArticleContent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportIpaVideosCommand extends Command
{
    protected $signature = 'articles:import-ipa-videos
                            {--website : 从 ipaau.org.cn/video 抓取}
                            {--bak : 从 bak/IPA视频 目录导入}
                            {--url=https://ipaau.org.cn/video/ : 抓取页面 URL}
                            {--html-file= : 使用本地 HTML 文件代替在线抓取}
                            {--bak-dir=bak/IPA视频 : 本地视频根目录}
                            {--broadcast-category=113 : IPA播报栏目 ID}
                            {--recap-category=114 : IPA活动回顾栏目 ID}
                            {--ffmpeg= : ffmpeg 可执行文件路径}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '导入 IPA 视频文章（网站抓取 + bak 本地目录）';

    /** @var array<string, int> */
    private array $stats = [
        'website_broadcast' => 0,
        'website_recap' => 0,
        'bak_broadcast' => 0,
        'bak_recap' => 0,
        'thumbnails' => 0,
        'skipped' => 0,
    ];

    public function handle(): int
    {
        $runWebsite = (bool) $this->option('website');
        $runBak = (bool) $this->option('bak');

        if (! $runWebsite && ! $runBak) {
            $runWebsite = true;
            $runBak = true;
        }

        if ($runWebsite) {
            $this->importFromWebsite(
                (int) $this->option('broadcast-category'),
                (int) $this->option('recap-category'),
            );
        }

        if ($runBak) {
            $this->importFromBak(
                base_path($this->option('bak-dir')),
                (int) $this->option('broadcast-category'),
                (int) $this->option('recap-category'),
            );
        }

        $this->newLine();
        $this->info('网站 IPA播报：'.$this->stats['website_broadcast'].' 篇');
        $this->info('网站 IPA活动回顾：'.$this->stats['website_recap'].' 篇');
        $this->info('本地 IPA播报：'.$this->stats['bak_broadcast'].' 篇');
        $this->info('本地 IPA活动回顾：'.$this->stats['bak_recap'].' 篇');
        $this->info('生成封面帧：'.$this->stats['thumbnails'].' 张');
        $this->info('跳过：'.$this->stats['skipped'].' 条');

        foreach ([(int) $this->option('broadcast-category'), (int) $this->option('recap-category')] as $categoryId) {
            $this->info('栏目 '.$categoryId.' 当前文章数：'.Article::query()->where('category_id', $categoryId)->count());
        }

        $this->info('请将视频与封面文件拷贝到 public/assets/video/');

        return self::SUCCESS;
    }

    private function importFromWebsite(int $broadcastCategoryId, int $recapCategoryId): void
    {
        $htmlFile = trim((string) $this->option('html-file'));
        $html = null;

        if ($htmlFile !== '') {
            $path = str_starts_with($htmlFile, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:/', $htmlFile)
                ? $htmlFile
                : base_path($htmlFile);

            if (! is_file($path)) {
                $this->error("HTML 文件不存在：{$path}");

                return;
            }

            $this->info("使用本地 HTML：{$path}");
            $html = file_get_contents($path);
        } else {
            $url = (string) $this->option('url');
            $this->info("抓取页面：{$url}");

            try {
                $response = Http::timeout(120)->get($url);

                if ($response->successful()) {
                    $html = $response->body();
                } else {
                    $this->warn('页面抓取失败：HTTP '.$response->status());
                }
            } catch (\Throwable $exception) {
                $this->warn('在线抓取失败：'.$exception->getMessage());
            }

            $fallback = storage_path('ipaau-video-page.html');

            if ($html === null && is_file($fallback)) {
                $this->warn("改用本地缓存：{$fallback}");
                $html = file_get_contents($fallback);
            }
        }

        if (! is_string($html) || $html === '') {
            $this->error('无法获取视频页面 HTML。');

            return;
        }
        $broadcastItems = IpaVideoPageScraper::scrapeSection($html, 'media');
        $recapItems = IpaVideoPageScraper::scrapeSection($html, 'function');

        $this->info('网站解析：IPA播报 '.count($broadcastItems).' 条，IPA活动回顾 '.count($recapItems).' 条');

        foreach ($broadcastItems as $item) {
            if ($this->upsertWebsiteArticle($broadcastCategoryId, $item, 'web-media')) {
                $this->stats['website_broadcast']++;
            }
        }

        foreach ($recapItems as $item) {
            if ($this->upsertWebsiteArticle($recapCategoryId, $item, 'web-function')) {
                $this->stats['website_recap']++;
            }
        }
    }

    /**
     * @param  array{title: string, video_file: string, cover_path: ?string}  $item
     */
    private function upsertWebsiteArticle(int $categoryId, array $item, string $sourceKey): bool
    {
        $videoStored = $this->resolveWebsiteVideoStoredPath($item['video_file'], $item['cover_path']);
        $coverStored = $item['cover_path'] !== null
            ? VideoArticleContent::rewriteLegacyAssetPath($item['cover_path'])
            : $this->defaultCoverPath($videoStored);

        return $this->upsertVideoArticle(
            categoryId: $categoryId,
            title: $item['title'],
            slugSeed: $sourceKey.'-'.$item['title'],
            videoStoredPath: $videoStored,
            coverStoredPath: $coverStored,
        );
    }

    private function importFromBak(string $baseDir, int $broadcastCategoryId, int $recapCategoryId): void
    {
        if (! is_dir($baseDir)) {
            $this->error("目录不存在：{$baseDir}");

            return;
        }

        $mapping = [
            'IPA播报' => $broadcastCategoryId,
            'IPA活动回顾' => $recapCategoryId,
        ];

        foreach ($mapping as $folder => $categoryId) {
            $dir = $this->findChildDirectory($baseDir, $folder);

            if ($dir === null) {
                $this->warn("未找到子目录：{$folder}");

                continue;
            }

            $files = glob($dir.'/*.{mp4,mkv,webm,mov}', GLOB_BRACE) ?: [];

            $this->info($folder.'：'.count($files).' 个视频文件');

            foreach ($files as $filePath) {
                if ($this->importBakVideo($categoryId, $folder, $filePath)) {
                    $this->stats[$folder === 'IPA播报' ? 'bak_broadcast' : 'bak_recap']++;
                }
            }
        }
    }

    private function importBakVideo(int $categoryId, string $folder, string $filePath): bool
    {
        $filename = basename($filePath);
        $title = VideoArticleContent::titleFromFilename($filename);

        if ($title === '') {
            $this->stats['skipped']++;

            return false;
        }

        $relativeFolder = str_replace('\\', '/', $folder);
        $videoStored = 'assets/video/'.$relativeFolder.'/'.$filename;
        $coverStored = 'assets/video/'.$relativeFolder.'/'.pathinfo($filename, PATHINFO_FILENAME).'.jpg';

        if (! $this->option('dry-run')) {
            $this->generateThumbnail($filePath, dirname($filePath).'/'.pathinfo($filename, PATHINFO_FILENAME).'.jpg');
        }

        return $this->upsertVideoArticle(
            categoryId: $categoryId,
            title: $title,
            slugSeed: 'bak-'.$relativeFolder.'-'.$filename,
            videoStoredPath: $videoStored,
            coverStoredPath: $coverStored,
        );
    }

    private function upsertVideoArticle(
        int $categoryId,
        string $title,
        string $slugSeed,
        string $videoStoredPath,
        string $coverStoredPath,
    ): bool {
        $slug = VideoArticleContent::makeSlug($categoryId, $title, substr(md5($slugSeed), 0, 8));
        $videoFilename = \App\Support\CategoryListTemplate\VideoListTemplate::normalizeVideoFilename($videoStoredPath);

        if ($this->option('dry-run')) {
            return true;
        }

        $payload = [
            'title' => $title,
            'category_id' => $categoryId,
            'content' => '',
            'summary' => null,
            'cover_image' => $coverStoredPath,
            'author' => null,
            'source' => null,
            'view_count' => 0,
            'published_at' => null,
            'is_active' => true,
            'is_featured' => false,
            'is_sticky' => false,
            'extra_fields' => [
                \App\Support\CategoryListTemplate\VideoListTemplate::VIDEO_FILENAME_KEY => $videoFilename,
            ],
        ];

        $article = Article::withTrashed()->where('slug', $slug)->first();

        if ($article !== null) {
            if ($article->trashed()) {
                $article->restore();
            }

            $article->fill($payload);
            $article->sort_order = $article->id;
            $article->save();
        } else {
            $article = Article::query()->create(array_merge($payload, [
                'slug' => $slug,
                'sort_order' => 0,
            ]));
            $article->sort_order = $article->id;
            $article->save();
        }

        return true;
    }

    private function resolveWebsiteVideoStoredPath(string $videoFile, ?string $coverPath): string
    {
        $videoFile = ltrim(str_replace('\\', '/', $videoFile), '/');

        if (str_contains($videoFile, '/')) {
            return VideoArticleContent::rewriteLegacyAssetPath($videoFile);
        }

        if ($coverPath !== null && $coverPath !== '') {
            $coverStored = VideoArticleContent::rewriteLegacyAssetPath($coverPath);
            $directory = dirname(str_replace('\\', '/', $coverStored));

            if ($directory !== '.' && $directory !== 'assets/video') {
                return $directory.'/'.basename($videoFile);
            }
        }

        return 'assets/video/course/'.basename($videoFile);
    }

    private function defaultCoverPath(string $videoStoredPath): string
    {
        $path = pathinfo($videoStoredPath);

        return ($path['dirname'] ?? 'assets/video').'/'.($path['filename'] ?? 'cover').'.jpg';
    }

    private function generateThumbnail(string $videoPath, string $thumbnailPath): void
    {
        if (is_file($thumbnailPath)) {
            return;
        }

        $ffmpeg = $this->resolveFfmpeg();

        if ($ffmpeg === null) {
            return;
        }

        $directory = dirname($thumbnailPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $command = sprintf(
            '%s -y -hide_banner -loglevel error -ss 00:00:01 -i %s -frames:v 1 -q:v 2 %s',
            escapeshellarg($ffmpeg),
            escapeshellarg($videoPath),
            escapeshellarg($thumbnailPath),
        );

        $result = null;
        exec($command, $output, $result);

        if ($result === 0 && is_file($thumbnailPath)) {
            $this->stats['thumbnails']++;
        }
    }

    private function resolveFfmpeg(): ?string
    {
        $configured = trim((string) $this->option('ffmpeg'));

        if ($configured !== '' && is_file($configured)) {
            return $configured;
        }

        $paths = [
            'D:\\Laragon\\bin\\ffmpeg\\ffmpeg.exe',
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        $which = trim((string) shell_exec('where ffmpeg 2>nul'));

        if ($which !== '' && is_file(explode("\n", $which)[0])) {
            return explode("\n", $which)[0];
        }

        return null;
    }

    private function findChildDirectory(string $baseDir, string $expectedName): ?string
    {
        foreach (scandir($baseDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $baseDir.DIRECTORY_SEPARATOR.$entry;

            if (! is_dir($path)) {
                continue;
            }

            if ($entry === $expectedName || str_contains($entry, str_replace('IPA', '', $expectedName))) {
                return $path;
            }
        }

        foreach (scandir($baseDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $baseDir.DIRECTORY_SEPARATOR.$entry;

            if (is_dir($path) && (str_contains($entry, '播报') || str_contains($entry, '活动'))) {
                if (str_contains($expectedName, '播报') && str_contains($entry, '播报')) {
                    return $path;
                }

                if (str_contains($expectedName, '活动') && str_contains($entry, '活动')) {
                    return $path;
                }
            }
        }

        return null;
    }
}
