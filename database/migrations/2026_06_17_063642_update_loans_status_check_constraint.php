<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE loans
            DROP CONSTRAINT loans_status_check
        ");

        DB::statement("
            ALTER TABLE loans
            ADD CONSTRAINT loans_status_check
            CHECK (
                status IN (
                    'pending',
                    'approved',
                    'disbursed',
                    'rejected',
                    'completed',
                    'overdue'
                )
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE loans
            DROP CONSTRAINT loans_status_check
        ");

        DB::statement("
            ALTER TABLE loans
            ADD CONSTRAINT loans_status_check
            CHECK (
                status IN (
                    'pending',
                    'approved',
                    'rejected',
                    'completed',
                    'overdue'
                )
            )
        ");
    }
};