<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained()->onDelete('cascade');
            $table->string('Full_name');
            $table->date('date_of_birth');
            $table->string('address');
            $table->string('id_card_picture'); // Stocke le chemin au lieu du binaire
            $table->string('client_picture'); // Stocke le chemin au lieu du binaire
            $table->string('id_card_number')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            // $table->boolean('is_assured')->default(false);
            // $table->boolean('is_payed')->default(false);
            $table->date('subscription_expired_date')->default(now()->toDateString());
            $table->date('assurance_expired_date')->default(now()->toDateString());

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
