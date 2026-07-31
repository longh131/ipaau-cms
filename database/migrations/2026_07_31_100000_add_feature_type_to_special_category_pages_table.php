<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('special_category_pages', function (Blueprint $table): void {
            $table->string('feature_type', 64)->default('course_list')->after('category_id');
            $table->string('certificate_title')->nullable()->after('course_category_ids');
            $table->text('certificate_summary')->nullable()->after('certificate_title');
        });
    }

    public function down(): void
    {
        Schema::table('special_category_pages', function (Blueprint $table): void {
            $table->dropColumn(['feature_type', 'certificate_title', 'certificate_summary']);
        });
    }
};
