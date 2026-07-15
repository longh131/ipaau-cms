<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (! Schema::hasColumn('articles', 'author')) {
                $table->string('author')->nullable()->after('summary');
            }
            if (! Schema::hasColumn('articles', 'source')) {
                $table->string('source')->nullable()->after('author');
            }
            if (! Schema::hasColumn('articles', 'view_count')) {
                $table->unsignedInteger('view_count')->default(0)->after('source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('articles', 'author') ? 'author' : null,
                Schema::hasColumn('articles', 'source') ? 'source' : null,
                Schema::hasColumn('articles', 'view_count') ? 'view_count' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
