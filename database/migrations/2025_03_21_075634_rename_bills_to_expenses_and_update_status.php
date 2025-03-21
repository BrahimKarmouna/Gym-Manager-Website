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
        // Rename the bills table to expenses
        Schema::rename('bills', 'expenses');

        // Update the status field to be an enum
        Schema::table('expenses', function (Blueprint $table) {
            // First drop the existing status column
            $table->dropColumn('status');
        });

        Schema::table('expenses', function (Blueprint $table) {
            // Then add it back as an enum
            $table->enum('status', ['paid', 'pending', 'overdue'])->default('pending');
            
            // Add a type field to distinguish between bills and gym enhancements
            $table->enum('type', ['bill', 'enhancement'])->default('bill');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First revert the status field
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['status', 'type']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });

        // Then rename the table back
        Schema::rename('expenses', 'bills');
    }
};
