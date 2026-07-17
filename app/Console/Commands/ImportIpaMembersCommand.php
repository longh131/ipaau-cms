<?php

namespace App\Console\Commands;

use App\Support\MemberImporter;
use Illuminate\Console\Command;

class ImportIpaMembersCommand extends Command
{
    protected $signature = 'members:import
                            {--file=bak/会员全数据.xlsx : Excel 文件路径（相对项目根目录）}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '从 Excel 全量覆盖导入持证会员数据';

    public function handle(MemberImporter $importer): int
    {
        $file = base_path($this->option('file'));
        $dryRun = (bool) $this->option('dry-run');

        try {
            $result = $importer->import($file, $dryRun);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if ($result['dry_run']) {
            $this->info("预览：将导入 {$result['imported']} 条，跳过 {$result['skipped']} 条（无证编号）。");
        } else {
            $this->info("导入完成：{$result['imported']} 条，跳过 {$result['skipped']} 条（无证编号）。");
        }

        return self::SUCCESS;
    }
}
