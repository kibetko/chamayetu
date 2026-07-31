<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_repayments', function (Blueprint $table) {

            $table->foreignId('user_id')
                ->after('loan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('mpesa_transaction_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();

            $table->string('transaction_code')
                ->nullable()
                ->after('reference');

        });
    }


    public function down(): void
    {
        Schema::table('loan_repayments', function (Blueprint $table) {

            $table->dropForeign([
                'user_id'
            ]);

            $table->dropForeign([
                'mpesa_transaction_id'
            ]);

            $table->dropColumn([
                'user_id',
                'mpesa_transaction_id',
                'transaction_code'
            ]);

        });
    }
};