<?php

use App\Models\Category;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Category::query()
            ->where('id', 80)
            ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_MEMBER_INTERVIEW]);
    }

    public function down(): void
    {
        Category::query()
            ->where('id', 80)
            ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE]);
    }
};
