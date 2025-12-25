<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gadgets', function (Blueprint $table) {
            $table->integer('ReorderPoint')->default(10)->after('BrandID')->comment('Minimum stock level before reordering');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gadgets', function (Blueprint $table) {
            $table->dropColumn('ReorderPoint');
        });
    }
};
