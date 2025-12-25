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
        Schema::create('inventory_audits', function (Blueprint $table) {
            $table->id('AuditID');
            $table->string('AuditNumber', 50)->unique()->comment('Audit/Stock Take Number');
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
            $table->integer('SystemQuantity')->default(0)->comment('Quantity in system');
            $table->integer('PhysicalQuantity')->default(0)->comment('Quantity physically counted');
            $table->integer('Variance')->default(0)->comment('Difference (Physical - System)');
            $table->text('Notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_audit_items');
        Schema::dropIfExists('inventory_audits');
    }
};
