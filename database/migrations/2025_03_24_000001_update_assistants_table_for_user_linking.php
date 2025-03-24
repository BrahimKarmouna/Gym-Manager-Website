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
        Schema::table('assistants', function (Blueprint $table) {
            // Make password nullable since assistants will now use user accounts
            $table->string('password')->nullable()->change();
            
            // Add user_account_id to link to a user account
            $table->foreignId('user_account_id')->nullable()->after('user_id')
                ->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assistants', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            $table->dropForeign(['user_account_id']);
            $table->dropColumn('user_account_id');
        });
    }
};
