<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rib', 128)->unique();
            $table->decimal('balance', 15, 2);
            $table->foreignId('user_id');
            $table->string('account_type');
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('total_income', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}

