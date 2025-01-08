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
        Schema::table('wallstreet_drink_events', function (Blueprint $table) {
            // add a active boolean to the wallstreet events
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallstreet_drink_events', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
