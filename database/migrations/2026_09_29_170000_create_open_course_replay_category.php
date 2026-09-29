<?php

use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use App\Support\CategoryListTemplate\VideoListTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $openCourse = DB::table('categories')->where('slug', VideoListTemplate::OPEN_COURSE_SLUG)->first();
        $now = now();

        $payload = [
            'name' => '公开课回放',
            'parent_id' => $openCourse->parent_id ?? 28,
            'type' => 'article',
            'list_template' => CategoryListTemplateRegistry::TEMPLATE_VIDEO_LIST,
            'article_extra_field_schema' => json_encode(VideoListTemplate::defaultExtraFieldSchema(), JSON_UNESCAPED_UNICODE),
            'introduction' => null,
            'sort_order' => 53,
            'is_active' => true,
            'requires_member_login' => false,
            'updated_at' => $now,
        ];

        $existing = DB::table('categories')->where('slug', VideoListTemplate::OPEN_COURSE_REPLAY_SLUG)->first();

        if ($existing) {
            DB::table('categories')->where('id', $existing->id)->update($payload);

            return;
        }

        DB::table('categories')->insert($payload + [
            'slug' => VideoListTemplate::OPEN_COURSE_REPLAY_SLUG,
            'created_at' => $now,
        ]);
    }

    public function down(): void
    {
        $category = DB::table('categories')->where('slug', VideoListTemplate::OPEN_COURSE_REPLAY_SLUG)->first();

        if ($category === null) {
            return;
        }

        $hasArticles = DB::table('articles')->where('category_id', $category->id)->exists();

        if ($hasArticles) {
            return;
        }

        DB::table('categories')->where('id', $category->id)->delete();
    }
};
