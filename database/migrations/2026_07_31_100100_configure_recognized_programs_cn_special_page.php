<?php

use App\Models\Category;
use App\Models\SpecialCategoryPage;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('categories')->where('id', 62)->exists()) {
            return;
        }

        DB::table('categories')
            ->where('id', 62)
            ->update([
                'list_template' => CategoryListTemplateRegistry::TEMPLATE_SPECIAL_CERTIFICATE_LOOKUP,
            ]);

        SpecialCategoryPage::query()->updateOrCreate(
            ['category_id' => 62],
            [
                'feature_type' => SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP,
                'body_html_top' => null,
                'body_html_bottom' => null,
                'course_category_ids' => null,
                'certificate_title' => '证书查询',
                'certificate_summary' => '请输入会员姓名与证书编号进行查询。',
            ],
        );
    }

    public function down(): void
    {
        SpecialCategoryPage::query()->where('category_id', 62)->delete();

        if (DB::table('categories')->where('id', 62)->exists()) {
            DB::table('categories')
                ->where('id', 62)
                ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE]);
        }
    }
};
