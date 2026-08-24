<?php

use App\Models\SpecialCategoryPage;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('categories')->where('id', 120)->exists()) {
            return;
        }

        DB::table('categories')
            ->where('id', 120)
            ->update([
                'name' => '我的CPD记录',
                'slug' => 'my-cpd-records',
                'type' => 'article',
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_SPECIAL_CPD_RECORDS,
            ]);

        SpecialCategoryPage::query()->updateOrCreate(
            ['category_id' => 120],
            [
                'feature_type' => SpecialCategoryPage::FEATURE_CPD_RECORDS,
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
        SpecialCategoryPage::query()->where('category_id', 120)->delete();

        if (DB::table('categories')->where('id', 120)->exists()) {
            DB::table('categories')
                ->where('id', 120)
                ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE]);
        }
    }
};
