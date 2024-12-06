<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropBankIsFirstColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bankaccounts', function ($table) {
            $table->dropColumn('is_first');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bankaccounts', function ($table) {
            $table->boolean('is_first')->default(false);
        });
    }
}
