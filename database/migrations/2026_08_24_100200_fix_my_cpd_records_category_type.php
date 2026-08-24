<?php

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
            ->update(['type' => 'article']);
    }

    public function down(): void
    {
        if (! DB::table('categories')->where('id', 120)->exists()) {
            return;
        }

        DB::table('categories')
            ->where('id', 120)
            ->update(['type' => 'page']);
    }
};
