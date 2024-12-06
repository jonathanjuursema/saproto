<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountToDinnerforms extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->renameColumn('discount', 'helper_discount');
        });
        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->float('regular_discount')->after('helper_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->dropColumn('regular_discount');
            $table->renameColumn('helper_discount', 'discount');
        });
    }
}
