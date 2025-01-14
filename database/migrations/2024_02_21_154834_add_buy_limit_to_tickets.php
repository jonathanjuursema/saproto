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
        Schema::table('tickets', function (Blueprint $table) {
            // add an integer column to the table
            $table->boolean('has_buy_limit')->default(false);
            $table->integer('buy_limit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // revert up
            $table->dropColumn('has_buy_limit');
            $table->dropColumn('buy_limit');
        });
    }
};
