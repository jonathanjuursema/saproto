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
        Schema::table('wallstreet_drink_prices', function (Blueprint $table) {
            // add float delta to the wallstreet_drink_prices table
            $table->float('diff')->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallstreet_drink_prices', function (Blueprint $table) {
            $table->dropColumn('diff');
        });
    }
};
