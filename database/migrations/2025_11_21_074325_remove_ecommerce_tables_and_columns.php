<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Removes e-commerce functionality: drops buyers, payments, transaction_items tables
     * and removes BuyerID column from transactions table.
     */
    public function up(): void
    {
        // Drop foreign key constraints first (only if tables/columns exist)
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'BuyerID')) {
            try {
                Schema::table('transactions', function (Blueprint $table) {
                    // Try to drop foreign key if it exists
                    $foreignKeys = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'transactions' 
                        AND COLUMN_NAME = 'BuyerID' 
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                    ");
                    if (!empty($foreignKeys)) {
                        $table->dropForeign(['BuyerID']);
                    }
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        if (Schema::hasTable('payments')) {
            try {
                Schema::table('payments', function (Blueprint $table) {
                    $table->dropForeign(['TransactionID']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        if (Schema::hasTable('transaction_items')) {
            try {
                Schema::table('transaction_items', function (Blueprint $table) {
                    $table->dropForeign(['TransactionID']);
                    $table->dropForeign(['GadgetID']);
                });
            } catch (\Exception $e) {
                // Foreign keys might not exist, continue
            }
        }

        // Drop tables in correct order (child tables first)
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('buyers');

        // Remove BuyerID column from transactions table (if it exists)
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'BuyerID')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('BuyerID');
            });
        }
    }

    /**
     * Reverse the migrations.
     * Note: This is a destructive migration. Reversing would require recreating
     * the tables and columns, but data will be lost.
     */
    public function down(): void
    {
        // Re-add BuyerID column to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('BuyerID')->nullable()->after('GadgetID');
            $table->foreign('BuyerID')->references('BuyerID')->on('buyers')->onDelete('set null');
        });

        // Recreate buyers table
        Schema::create('buyers', function (Blueprint $table) {
            $table->id('BuyerID');
            $table->string('BuyerName', 100);
            $table->string('Phone', 20)->nullable();
            $table->string('Email', 100)->nullable()->unique();
            $table->string('PasswordHash')->nullable();
            $table->timestamps();
        });

        // Recreate payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id('PaymentID');
            $table->unsignedBigInteger('TransactionID');
            $table->string('Method', 20);
            $table->decimal('Amount', 10, 2);
            $table->dateTime('PaymentDate');
            $table->timestamps();
            $table->foreign('TransactionID')->references('TransactionID')->on('transactions')->onDelete('cascade');
        });

        // Recreate transaction_items table
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id('TransactionItemID');
            $table->unsignedBigInteger('TransactionID');
            $table->unsignedBigInteger('GadgetID');
            $table->integer('Quantity');
            $table->decimal('PriceAtSale', 10, 2);
            $table->timestamps();
            $table->foreign('TransactionID')->references('TransactionID')->on('transactions')->onDelete('cascade');
            $table->foreign('GadgetID')->references('GadgetID')->on('gadgets')->onDelete('cascade');
        });
    }
};
