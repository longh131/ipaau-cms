<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\PageComponent;
use App\Models\Setting;
use App\Support\MediaUrl;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NormalizeRichContentUrlsCommand extends Command
{
    protected $signature = 'rich-content:normalize-urls
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '将富文本中的 storage 图片绝对 URL 统一改为 /storage/... 相对路径';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updatedRows = 0;

        $this->info($dryRun ? '预览模式（不会写入数据库）' : '开始规范化富文本图片 URL…');

        $updatedRows += $this->normalizeModelColumn(Article::class, 'content', $dryRun);
        $updatedRows += $this->normalizeModelColumn(Article::class, 'summary', $dryRun);
        $updatedRows += $this->normalizeModelColumn(Page::class, 'content', $dryRun);
        $updatedRows += $this->normalizeModelColumn(Page::class, 'data', $dryRun, isJson: true);
        $updatedRows += $this->normalizeModelColumn(Category::class, 'introduction', $dryRun);
        $updatedRows += $this->normalizeModelColumn(PageComponent::class, 'data', $dryRun, isJson: true);
        $updatedRows += $this->normalizeSettings($dryRun);

        $this->newLine();
        $this->info($dryRun
            ? "预览完成，预计更新 {$updatedRows} 条记录。"
            : "完成，已更新 {$updatedRows} 条记录。");

        return self::SUCCESS;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function normalizeModelColumn(
        string $modelClass,
        string $column,
        bool $dryRun,
        bool $isJson = false,
    ): int {
        /** @var Model $modelClass */
        if (! $modelClass::query()->exists()) {
            return 0;
        }

        $updated = 0;
        $table = (new $modelClass)->getTable();

        $modelClass::query()
            ->select(['id', $column])
            ->orderBy('id')
            ->chunkById(100, function ($rows) use (&$updated, $column, $dryRun, $isJson, $table, $modelClass): void {
                foreach ($rows as $row) {
                    $original = $row->{$column};

                    if ($this->isBlankValue($original)) {
                        continue;
                    }

                    $normalized = $isJson
                        ? MediaUrl::normalizeRichContentValue($original)
                        : MediaUrl::normalizeRichContentValue($original);

                    if ($normalized === $original) {
                        continue;
                    }

                    $updated++;

                    $this->line(sprintf(
                        '[%s.%s] #%s',
                        $table,
                        $column,
                        $row->getKey(),
                    ));

                    if ($dryRun) {
                        continue;
                    }

                    DB::table($table)
                        ->where('id', $row->getKey())
                        ->update([
                            $column => $isJson
                                ? json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
                                : $normalized,
                        ]);
                }
            });

        return $updated;
    }

    protected function normalizeSettings(bool $dryRun): int
    {
        $updated = 0;

        Setting::query()
            ->orderBy('id')
            ->chunkById(100, function ($rows) use (&$updated, $dryRun): void {
                foreach ($rows as $setting) {
                    $original = $setting->value;
                    $normalized = MediaUrl::normalizeRichContentValue($original);

                    if ($normalized === $original) {
                        continue;
                    }

                    $updated++;
                    $this->line("[settings.value] key={$setting->key}");

                    if ($dryRun) {
                        continue;
                    }

                    $setting->update(['value' => $normalized]);
                }
            });

        return $updated;
    }

    protected function isBlankValue(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value) && trim($value) === '') {
            return true;
        }

        if (is_array($value) && $value === []) {
            return true;
        }

        return false;
    }
}
