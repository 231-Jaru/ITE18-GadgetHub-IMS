<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove SellingPrice column from stocks table as system is inventory-only
     */
    public function up(): void
    {
        if (Schema::hasTable('stocks') && Schema::hasColumn('stocks', 'SellingPrice')) {
        Schema::table('stocks', function (Blueprint $table) {
                $table->dropColumn('SellingPrice');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('stocks') && !Schema::hasColumn('stocks', 'SellingPrice')) {
        Schema::table('stocks', function (Blueprint $table) {
                $table->decimal('SellingPrice', 10, 2)->nullable()->after('CostPrice');
        });
        }
    }
};
