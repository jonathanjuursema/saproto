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
        // add a random number of minutes to the wallstreet events
        Schema::table('wallstreet_drink', function (Blueprint $table) {
            $table->integer('random_events_chance')->default(0);
            $table->dropColumn('random_events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // reverse the migration
        Schema::table('wallstreet_drink', function (Blueprint $table) {
            $table->dropColumn('random_events_chance');
            $table->boolean('random_events')->default(false);
        });
    }
};
