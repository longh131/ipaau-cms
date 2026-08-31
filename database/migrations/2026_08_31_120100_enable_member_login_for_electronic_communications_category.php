<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Category::query()
            ->where('slug', 'electronic-communications')
            ->update(['requires_member_login' => true]);
    }

    public function down(): void
    {
        Category::query()
            ->where('slug', 'electronic-communications')
            ->update(['requires_member_login' => false]);
    }
};
