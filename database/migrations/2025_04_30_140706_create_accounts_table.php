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
            $table->string('id')->primary()->unique();
            $table->string('name')->nullable();
            $table->string('iban')->nullable();
            $table->string('bban')->nullable();
            $table->string('status')->nullable();
            $table->string('owner_name')->nullable();
            $table->timestamp('created')->useCurrent();
            $table->timestamp('last_accessed')->useCurrent();

            $table->dateTime('transactions_disabled_date')->nullable();
            $table->dateTime('balance_disabled_date')->nullable();

            $table->unsignedBigInteger('institution_id');
            $table->foreign('institution_id')->references('id')->on('institutions')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
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
