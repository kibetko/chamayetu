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
         Schema::table('loan_approvals', function (Blueprint $table) {

        if (!Schema::hasColumn('loan_approvals', 'comment')) {
            $table->text('comment')->nullable();
        }

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_approvals', function (Blueprint $table) {
            //
        });
    }
};
