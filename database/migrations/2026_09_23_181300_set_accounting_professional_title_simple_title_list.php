<?php

use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')
            ->where('slug', 'accounting-professional-title')
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE_TITLE_LIST,
            ]);
    }

    public function down(): void
    {
        DB::table('categories')
            ->where('slug', 'accounting-professional-title')
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE,
            ]);
    }
};
