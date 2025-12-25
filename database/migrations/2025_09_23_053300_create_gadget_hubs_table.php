<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Admins
        Schema::create('admins', function (Blueprint $table) {
            $table->id('AdminID');
            $table->string('Username', 50)->unique();
            $table->string('PasswordHash', 255);
            $table->enum('Role', ['Admin', 'Staff'])->default('Staff');
            $table->timestamps();
        });

        // 2. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('SupplierID');
            $table->string('SupplierName', 100);
            $table->string('ContactPerson', 100)->nullable();
            $table->string('Phone', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->timestamps();
        });

        // 3. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id('CategoryID');
            $table->string('CategoryName', 100);
            $table->timestamps();
        });

        // 4. Brands
        Schema::create('brands', function (Blueprint $table) {
            $table->id('BrandID');
            $table->string('BrandName', 100);
            $table->timestamps();
        });

        // 5. Gadgets (basic info only)
        Schema::create('gadgets', function (Blueprint $table) {
            $table->id('GadgetID');
            $table->string('GadgetName', 100);
            $table->foreignId('CategoryID')->nullable()->constrained('categories', 'CategoryID')->nullOnDelete();
            $table->foreignId('BrandID')->nullable()->constrained('brands', 'BrandID')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Stocks (tracks purchases & prices)
        Schema::create('stocks', function (Blueprint $table) {
            $table->id('StockID');
            $table->foreignId('GadgetID')->constrained('gadgets', 'GadgetID')->cascadeOnDelete();
            $table->foreignId('SupplierID')->nullable()->constrained('suppliers', 'SupplierID')->nullOnDelete();
            $table->integer('QuantityAdded');
            $table->decimal('CostPrice', 10, 2);   // purchase price
            $table->decimal('SellingPrice', 10, 2); // suggested retail price
            $table->timestamp('PurchaseDate')->useCurrent();
            $table->timestamps();
        });

        // 7. Buyers
        Schema::create('buyers', function (Blueprint $table) {
            $table->id('BuyerID');
            $table->string('BuyerName', 100);
            $table->string('Phone', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->timestamps();
        });

        // 8. Transactions
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('TransactionID');
            $table->foreignId('GadgetID')->constrained('gadgets', 'GadgetID')->cascadeOnDelete();
            $table->foreignId('BuyerID')->nullable()->constrained('buyers', 'BuyerID')->nullOnDelete();
            $table->foreignId('AdminID')->nullable()->constrained('admins', 'AdminID')->nullOnDelete();
            $table->foreignId('StockID')->nullable()->constrained('stocks', 'StockID')->nullOnDelete();
            $table->enum('TransactionType', ['IN', 'OUT']);// IN = restock, OUT = sale
            $table->integer('Quantity');
            $table->timestamp('TransactionDate')->useCurrent();
            $table->timestamps();
        });

        // 9. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id('PaymentID');
            $table->foreignId('TransactionID')->constrained('transactions', 'TransactionID')->cascadeOnDelete();
            $table->string('Method', 50);
            $table->decimal('Amount', 10, 2);
            $table->timestamp('PaymentDate')->useCurrent();
            $table->timestamps();
        });

        // 10. TransactionItems 
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id('TransactionItemID');
            $table->foreignId('TransactionID')->constrained('transactions', 'TransactionID')->cascadeOnDelete();
            $table->foreignId('GadgetID')->constrained('gadgets', 'GadgetID')->cascadeOnDelete();
            $table->integer('Quantity');
            $table->decimal('PriceAtSale', 10, 2); // keeps historical selling price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('buyers');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('gadgets');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('admins');
    }
};