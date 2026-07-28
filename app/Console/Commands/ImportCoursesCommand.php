<?php

namespace App\Console\Commands;

use App\Support\CourseImporter;
use Illuminate\Console\Command;

class ImportCoursesCommand extends Command
{
    protected $signature = 'courses:import
                            {--file=bak/IPA_book.xlsx : Excel 文件路径（相对项目根目录）}
                            {--dry-run : 仅预览，不写入数据库}';

    protected $description = '从 Excel 全量覆盖导入课程数据';

    public function handle(CourseImporter $importer): int
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
            $this->info("预览：将导入 {$result['imported']} 条；跳过无【】 {$result['skipped_no_bracket']} 条，空标题 {$result['skipped_empty']} 条。");
        } else {
            $this->info("导入完成：{$result['imported']} 条；跳过无【】 {$result['skipped_no_bracket']} 条，空标题 {$result['skipped_empty']} 条。");
        }

        return self::SUCCESS;
    }
}
