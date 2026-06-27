<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('phone', 30)->nullable()->after('name');
            $table->string('company')->nullable()->after('email');
            $table->string('job_title')->nullable()->after('company');
            $table->string('education')->nullable()->after('job_title');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->unique('email');
            $table->dropColumn(['name', 'phone', 'company', 'job_title', 'education']);
        });
    }
};
