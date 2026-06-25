<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_items')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            if (! Schema::hasColumn('menu_items', 'megamenu_image_alt')) {
                $table->string('megamenu_image_alt')->nullable()->after('icon');
            }
            if (! Schema::hasColumn('menu_items', 'megamenu_promo_text')) {
                $table->string('megamenu_promo_text')->nullable()->after('megamenu_image_alt');
            }
            if (! Schema::hasColumn('menu_items', 'megamenu_promo_url')) {
                $table->string('megamenu_promo_url', 500)->nullable()->after('megamenu_promo_text');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('menu_items')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            $columns = ['megamenu_promo_url', 'megamenu_promo_text', 'megamenu_image_alt'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('menu_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
