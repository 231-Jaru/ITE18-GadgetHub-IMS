<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove unused purchase orders and inventory audits tables to keep system simple.
     * These features can be added later if needed.
     */
    public function up(): void
    {
        // Drop child tables first
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('inventory_audit_items');
        
        // Drop parent tables
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('inventory_audits');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate purchase orders tables
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('PurchaseOrderID');
            $table->string('PONumber', 50)->unique();
            $table->foreignId('SupplierID')->constrained('suppliers', 'SupplierID')->cascadeOnDelete();
            $table->foreignId('AdminID')->nullable()->constrained('admins', 'AdminID')->nullOnDelete();
            $table->enum('Status', ['DRAFT', 'PENDING', 'ORDERED', 'RECEIVED', 'CANCELLED'])->default('DRAFT');
            $table->decimal('TotalAmount', 10, 2)->default(0);
            $table->date('OrderDate')->nullable();
            $table->date('ExpectedDeliveryDate')->nullable();
            $table->date('ReceivedDate')->nullable();
            $table->text('Notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id('POItemID');
            $table->foreignId('PurchaseOrderID')->constrained('purchase_orders', 'PurchaseOrderID')->cascadeOnDelete();
            $table->foreignId('GadgetID')->constrained('gadgets', 'GadgetID')->cascadeOnDelete();
            $table->integer('Quantity')->default(1);
            $table->decimal('UnitCost', 10, 2);
            $table->decimal('TotalCost', 10, 2);
            $table->integer('QuantityReceived')->default(0);
            $table->timestamps();
        });

        // Recreate inventory audits tables
        Schema::create('inventory_audits', function (Blueprint $table) {
            $table->id('AuditID');
            $table->string('AuditNumber', 50)->unique();
            $table->foreignId('AdminID')->nullable()->constrained('admins', 'AdminID')->nullOnDelete();
            $table->enum('Status', ['DRAFT', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('DRAFT');
            $table->date('AuditDate');
            $table->text('Notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_audit_items', function (Blueprint $table) {
            $table->id('AuditItemID');
            $table->foreignId('AuditID')->constrained('inventory_audits', 'AuditID')->cascadeOnDelete();
            $table->foreignId('GadgetID')->constrained('gadgets', 'GadgetID')->cascadeOnDelete();
            $table->foreignId('StockID')->nullable()->constrained('stocks', 'StockID')->nullOnDelete();
            $table->integer('SystemQuantity')->default(0);
            $table->integer('PhysicalQuantity')->default(0);
            $table->integer('Variance')->default(0);
            $table->text('Notes')->nullable();
            $table->timestamps();
        });
    }
};
