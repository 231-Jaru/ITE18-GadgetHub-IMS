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
        // Add indexes for better performance
        Schema::table('stocks', function (Blueprint $table) {
            $table->index('QuantityAdded');
            $table->index(['GadgetID', 'QuantityAdded']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('TransactionDate');
            $table->index(['AdminID', 'TransactionDate']);
            $table->index(['GadgetID', 'TransactionDate']);
        });

        Schema::table('gadgets', function (Blueprint $table) {
            $table->index('CategoryID');
            $table->index('BrandID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropIndex(['QuantityAdded']);
            $table->dropIndex(['GadgetID', 'QuantityAdded']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['TransactionDate']);
            $table->dropIndex(['AdminID', 'TransactionDate']);
            $table->dropIndex(['GadgetID', 'TransactionDate']);
        });

        Schema::table('gadgets', function (Blueprint $table) {
            $table->dropIndex(['CategoryID']);
            $table->dropIndex(['BrandID']);
        });
    }
};