<?php

namespace App\Console\Commands;

use App\Support\ArticleSortOrder;
use Illuminate\Console\Command;

class FixMembershipSortOrderCommand extends Command
{
    protected $signature = 'articles:fix-membership-sort-order
                            {--categories=79,80,81 : 栏目 ID，逗号分隔}';

    protected $description = '修正会员风采/会员专访/会员分享栏目的 sort_order（越大越靠前，与全站一致）';

    public function handle(): int
    {
        $categoryIds = collect(explode(',', (string) $this->option('categories')))
            ->map(fn (string $id): int => (int) trim($id))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($categoryIds->isEmpty()) {
            $this->error('请指定有效的栏目 ID。');

            return self::FAILURE;
        }

        foreach ($categoryIds as $categoryId) {
            $updated = ArticleSortOrder::mirrorImportOrderForCategory($categoryId);
            $this->info("栏目 {$categoryId}：已更新 {$updated} 篇文章的 sort_order。");
        }

        return self::SUCCESS;
    }
}
