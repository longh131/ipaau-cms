<?php

use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('categories')->where('id', 69)->exists()) {
            return;
        }

        DB::table('categories')
            ->where('id', 69)
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_EVENTS_CPD,
            ]);
    }

    public function down(): void
    {
        if (! DB::table('categories')->where('id', 69)->exists()) {
            return;
        }

        DB::table('categories')
            ->where('id', 69)
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE,
            ]);
    }
};
