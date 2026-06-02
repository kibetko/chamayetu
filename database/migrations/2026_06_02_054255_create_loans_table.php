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
        Schema::create('loans', function (Blueprint $table) {
    $table->id();

    $table->foreignId('group_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->decimal('amount', 12, 2);

    $table->decimal('interest_rate', 5, 2);

    $table->integer('duration_days');

    $table->text('reason')->nullable();

    $table->enum('status', [
        'pending',
        'approved',
        'rejected',
        'completed',
        'overdue'
    ])->default('pending');

    $table->timestamp('approved_at')->nullable();

    $table->timestamp('disbursed_at')->nullable();

    $table->date('due_date')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
