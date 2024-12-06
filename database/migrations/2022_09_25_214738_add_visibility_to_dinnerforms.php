<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilityToDinnerforms extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->boolean('visible_home_page')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->dropColumn('visible_home_page');
        });
    }
}
