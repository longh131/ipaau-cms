<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\SpecialCategoryPage;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('categories')->where('id', 28)->exists()) {
            return;
        }

        DB::table('categories')
            ->where('id', 28)
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_SPECIAL_COURSE_LIST,
            ]);

        SpecialCategoryPage::query()->firstOrCreate(
            ['category_id' => 28],
            [
                'body_html_top' => null,
                'body_html_bottom' => null,
                'course_category_ids' => Course::categoryIds(),
            ],
        );
    }

    public function down(): void
    {
        SpecialCategoryPage::query()->where('category_id', 28)->delete();

        if (DB::table('categories')->where('id', 28)->exists()) {
            DB::table('categories')
                ->where('id', 28)
                ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE]);
        }
    }
};
