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
        Schema::create('accounts', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('user_id');
          $table->string('account_name');
          $table->decimal('balance', 15, 2);
          $table->string('currency', 3);
          $table->string('account_type'); // Set account types
          $table->timestamps();

          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
