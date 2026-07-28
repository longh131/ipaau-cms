<?php

use App\Models\Category;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Category::query()
            ->whereIn('id', [48, 49, 50, 54])
            ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_COURSE_TABLE]);
    }

    public function down(): void
    {
        Category::query()
            ->whereIn('id', [48, 49, 50, 54])
            ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE]);
    }
};
