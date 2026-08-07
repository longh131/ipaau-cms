<?php

use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')
            ->whereIn('id', [113, 114])
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_VIDEO_LIST,
            ]);
    }

    public function down(): void
    {
        DB::table('categories')
            ->whereIn('id', [113, 114])
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE,
            ]);
    }
};
