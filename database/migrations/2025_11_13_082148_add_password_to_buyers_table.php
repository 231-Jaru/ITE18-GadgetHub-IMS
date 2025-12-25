<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: This migration is now obsolete as the buyers table has been removed.
     * Keeping it for migration history but making it a no-op.
     */
    public function up(): void
    {
        // Buyers table has been removed - this migration is now a no-op
        if (Schema::hasTable('buyers')) {
            Schema::table('buyers', function (Blueprint $table) {
                $table->string('PasswordHash', 255)->nullable()->after('Email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Buyers table has been removed - this migration is now a no-op
        if (Schema::hasTable('buyers')) {
            Schema::table('buyers', function (Blueprint $table) {
                $table->dropColumn('PasswordHash');
            });
        }
    }
};
