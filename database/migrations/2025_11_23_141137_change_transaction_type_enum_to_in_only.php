<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change TransactionType enum from ['IN', 'OUT'] to ['IN'] only
     * System only handles inventory restocking (IN transactions)
     */
    public function up(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'TransactionType')) {
            // First, update any 'OUT' transactions to 'IN' (shouldn't exist, but just in case)
            DB::table('transactions')
                ->where('TransactionType', 'OUT')
                ->update(['TransactionType' => 'IN']);
            
            // Then change the enum to only allow 'IN'
            // MySQL doesn't support direct enum modification, so we need to use raw SQL
            DB::statement("ALTER TABLE transactions MODIFY COLUMN TransactionType ENUM('IN') NOT NULL COMMENT 'Transaction type - IN = restock'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'TransactionType')) {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN TransactionType ENUM('IN', 'OUT') NOT NULL COMMENT 'IN = restock, OUT = sale'");
        }
    }
};
