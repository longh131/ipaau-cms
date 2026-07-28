<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->date('legacy_added_at')->nullable()->change();
            $table->date('starts_at')->nullable()->change();
            $table->date('registration_deadline')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dateTime('legacy_added_at')->nullable()->change();
            $table->dateTime('starts_at')->nullable()->change();
            $table->dateTime('registration_deadline')->nullable()->change();
        });
    }
};
