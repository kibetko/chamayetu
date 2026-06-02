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
        Schema::create('group_settings', function (Blueprint $table) {
    $table->id();

    $table->foreignId('group_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->decimal('interest_rate', 5, 2)->default(10);

    $table->integer('repayment_period_days')->default(90);

    $table->integer('grace_period_days')->default(7);

    $table->decimal('late_penalty_amount', 10, 2)->default(500);

    $table->enum('late_penalty_type', [
        'fixed',
        'percentage'
    ])->default('fixed');

    $table->decimal('minimum_contribution', 10, 2)->default(1000);

    $table->integer('maximum_loan_multiplier')->default(3);

    $table->foreignId('updated_by')
          ->nullable()
          ->constrained('users')
          ->nullOnDelete();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_settings');
    }
};
