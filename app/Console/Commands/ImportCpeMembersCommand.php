<?php

namespace App\Console\Commands;

use App\Support\CpeMemberImporter;
use Illuminate\Console\Command;

class ImportCpeMembersCommand extends Command
{
    protected $signature = 'cpe-members:import
                            {--file=bak/cpe_member.xlsx : Excel 文件路径}
                            {--append : 追加导入，不删除现有数据}';

    protected $description = '从 Excel 导入 CPE 会员报名记录（cpe_members）';

    public function handle(CpeMemberImporter $importer): int
    {
        $file = (string) $this->option('file');
        $path = str_starts_with($file, DIRECTORY_SEPARATOR) || preg_match('#^[A-Za-z]:\\\\#', $file)
            ? $file
            : base_path($file);

        if (! is_file($path)) {
            $this->error("文件不存在：{$path}");

            return self::FAILURE;
        }

        try {
            $result = $importer->import($path, truncate: ! $this->option('append'));
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("已导入 {$result['imported']} 条。");
        $this->line("跳过空行：{$result['skipped_empty']}");
        $this->line("未匹配课程（仅保留 book_legacy_id）：{$result['unresolved_courses']}");

        return self::SUCCESS;
    }
}
