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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('PurchaseOrderID');
            $table->string('PONumber', 50)->unique()->comment('Purchase Order Number');
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
            $table->integer('QuantityReceived')->default(0)->comment('Quantity actually received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
