<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('article_url', 2048)->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('legacy_added_at')->nullable();
            $table->string('registration_url', 2048)->nullable();
            $table->longText('content')->nullable();
            $table->string('city')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('registration_deadline')->nullable();
            $table->decimal('cpd_credits', 8, 2)->nullable();
            $table->string('price')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
