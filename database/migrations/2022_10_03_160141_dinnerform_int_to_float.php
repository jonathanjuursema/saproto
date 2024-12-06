<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DinnerformIntToFloat extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->float('discount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->integer('discount')->change();
        });
    }
}
