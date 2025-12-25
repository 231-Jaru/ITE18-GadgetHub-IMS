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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id('AdjustmentID');
            $table->foreignId('GadgetID')->constrained('gadgets', 'GadgetID')->cascadeOnDelete();
            $table->foreignId('StockID')->nullable()->constrained('stocks', 'StockID')->nullOnDelete();
            $table->foreignId('AdminID')->nullable()->constrained('admins', 'AdminID')->nullOnDelete();
            $table->enum('AdjustmentType', ['INCREASE', 'DECREASE', 'SET'])->comment('Type of adjustment');
            $table->integer('QuantityBefore')->default(0)->comment('Stock quantity before adjustment');
            $table->integer('QuantityAfter')->default(0)->comment('Stock quantity after adjustment');
            $table->integer('QuantityChanged')->default(0)->comment('Amount changed (positive or negative)');
            $table->string('Reason', 255)->nullable()->comment('Reason for adjustment (damage, found, correction, etc.)');
            $table->text('Notes')->nullable();
            $table->timestamp('AdjustmentDate')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
