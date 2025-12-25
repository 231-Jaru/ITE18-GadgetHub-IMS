<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove ReorderQuantity column from gadgets table (simplified to only use ReorderPoint)
     */
    public function up(): void
    {
        if (Schema::hasTable('gadgets') && Schema::hasColumn('gadgets', 'ReorderQuantity')) {
            Schema::table('gadgets', function (Blueprint $table) {
                $table->dropColumn('ReorderQuantity');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('gadgets') && !Schema::hasColumn('gadgets', 'ReorderQuantity')) {
            Schema::table('gadgets', function (Blueprint $table) {
                $table->integer('ReorderQuantity')->nullable()->after('ReorderPoint')->comment('Recommended quantity to reorder');
            });
        }
    }
};
