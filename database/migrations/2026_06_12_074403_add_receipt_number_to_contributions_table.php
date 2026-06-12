<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {

            $table->foreignId('mpesa_transaction_id')
                ->nullable()
                ->after('id')
                ->constrained('mpesa_transactions')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {

            $table->dropForeign(['mpesa_transaction_id']);
            $table->dropColumn('mpesa_transaction_id');

        });
    }
};