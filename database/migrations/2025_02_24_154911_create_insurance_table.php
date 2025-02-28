<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            // Foreign key references
            $table->foreignId('client_id')
                ->constrained();  // This will reference the 'id' column of the 'clients' table

            $table->foreignId('insurance_plan_id')
                ->constrained(); // This will reference the 'id' column of the 'insurance_plans' table
            $table->date('payment_date');
            $table->date('expiry_date')->default(now()->addYear());
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
