<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpe_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->unsignedBigInteger('book_legacy_id')->nullable()->index();
            $table->string('member_number', 32)->index();
            $table->unsignedTinyInteger('attend')->default(2);
            $table->string('registration_method')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'member_number']);
            $table->unique(['book_legacy_id', 'member_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpe_members');
    }
};
