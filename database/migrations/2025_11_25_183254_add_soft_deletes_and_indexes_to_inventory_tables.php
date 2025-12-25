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
        // Add soft deletes to critical tables
        Schema::table('admins', function (Blueprint $table) {
            $table->softDeletes('deleted_at')->nullable();
            $table->index('deleted_at');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->softDeletes('deleted_at')->nullable();
            $table->index('deleted_at');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes('deleted_at')->nullable();
            $table->unique('CategoryName'); // Add unique constraint
            $table->index('deleted_at');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->softDeletes('deleted_at')->nullable();
            $table->unique('BrandName'); // Add unique constraint
            $table->index('deleted_at');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->softDeletes('deleted_at')->nullable();
            $table->index('deleted_at');
            $table->index('PurchaseDate');
        });

        // Add indexes to existing tables for better performance
        Schema::table('gadgets', function (Blueprint $table) {
            $table->index('deleted_at'); // Gadgets already has soft deletes
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('AdminID');
            $table->index('StockID');
            $table->index('TransactionType');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('SupplierID');
            $table->index('AdminID');
            $table->index('Status');
            $table->index('OrderDate');
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->index('PurchaseOrderID');
            $table->index('GadgetID');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->index('GadgetID');
            $table->index('StockID');
            $table->index('AdminID');
            $table->index('AdjustmentDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove soft deletes
        Schema::table('admins', function (Blueprint $table) {
            $table->dropSoftDeletes('deleted_at');
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropSoftDeletes('deleted_at');
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes('deleted_at');
            $table->dropUnique(['CategoryName']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropSoftDeletes('deleted_at');
            $table->dropUnique(['BrandName']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropSoftDeletes('deleted_at');
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['PurchaseDate']);
        });

        // Remove indexes
        Schema::table('gadgets', function (Blueprint $table) {
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['AdminID']);
            $table->dropIndex(['StockID']);
            $table->dropIndex(['TransactionType']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['SupplierID']);
            $table->dropIndex(['AdminID']);
            $table->dropIndex(['Status']);
            $table->dropIndex(['OrderDate']);
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropIndex(['PurchaseOrderID']);
            $table->dropIndex(['GadgetID']);
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropIndex(['GadgetID']);
            $table->dropIndex(['StockID']);
            $table->dropIndex(['AdminID']);
            $table->dropIndex(['AdjustmentDate']);
        });
    }
};
