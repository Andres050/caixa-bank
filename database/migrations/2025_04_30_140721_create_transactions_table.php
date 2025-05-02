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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id')->primary()->unique();
            $table->string('entryReference')->nullable();
            $table->string('checkId')->nullable();
            $table->dateTime('bookingDate')->nullable();
            $table->dateTime('valueDate')->nullable();
            $table->decimal('transactionAmount_amount', 15, 2)->nullable();
            $table->string('transactionAmount_currency')->nullable();
            $table->string('remittanceInformationUnstructured')->nullable();
            $table->string('bankTransactionCode')->nullable();
            $table->string('proprietaryBankTransactionCode')->nullable();
            $table->string('internalTransactionId')->nullable();


            $table->string('debtorName')->nullable();
            $table->text('debtorAccount')->nullable();

            $table->string('account_id');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
