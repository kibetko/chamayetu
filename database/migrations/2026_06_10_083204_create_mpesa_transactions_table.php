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
        Schema::create('mpesa_transactions', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id');
    $table->foreignId('group_id');

    $table->decimal('amount', 12, 2);

    $table->string('phone');

    $table->string('checkout_request_id')->nullable();

    $table->string('merchant_request_id')->nullable();

    $table->string('receipt_number')->nullable();

    $table->string('status')->default('pending');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
