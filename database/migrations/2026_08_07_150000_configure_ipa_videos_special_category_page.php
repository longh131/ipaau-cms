<?php

use App\Models\SpecialCategoryPage;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('categories')->where('id', 71)->exists()) {
            return;
        }

        DB::table('categories')
            ->where('id', 71)
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_SPECIAL_VIDEO_HUB,
            ]);

        SpecialCategoryPage::query()->updateOrCreate(
            ['category_id' => 71],
            [
                'feature_type' => SpecialCategoryPage::FEATURE_VIDEO_HUB,
                'body_html_top' => null,
                'body_html_bottom' => null,
                'course_category_ids' => null,
                'certificate_title' => null,
                'certificate_summary' => null,
            ],
        );
    }

    public function down(): void
    {
        SpecialCategoryPage::query()->where('category_id', 71)->delete();

        if (DB::table('categories')->where('id', 71)->exists()) {
            DB::table('categories')
                ->where('id', 71)
                ->update([
                    'list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE,
                ]);
        }
    }
};
