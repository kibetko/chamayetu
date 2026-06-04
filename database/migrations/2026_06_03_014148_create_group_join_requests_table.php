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
      Schema::create('group_join_requests', function (Blueprint $table) {

    $table->id();

    $table->foreignId('group_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->string('phone_number');

    $table->text('message')->nullable();

    $table->enum('status', [
        'pending',
        'approved',
        'rejected'
    ])->default('pending');

    $table->foreignId('reviewed_by')
          ->nullable()
          ->constrained('users')
          ->nullOnDelete();

    $table->timestamp('reviewed_at')
          ->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_join_requests');
    }
};
