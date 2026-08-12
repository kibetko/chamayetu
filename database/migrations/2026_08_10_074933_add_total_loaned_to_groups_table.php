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
    Schema::table('groups', function (Blueprint $table) {
        $table->decimal('total_loaned', 12, 2)
            ->default(0)
            ->after('total_contributions');
    });
}

public function down(): void
{
    Schema::table('groups', function (Blueprint $table) {
        $table->dropColumn('total_loaned');
    });
}
};
