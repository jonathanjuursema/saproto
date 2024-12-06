<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddDatesToFlickr extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('flickr_albums', function ($table) {
            $table->integer('date_create');
            $table->integer('date_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flickr_albums', function ($table) {
            $table->dropColumn('date_create');
            $table->dropColumn('date_update');
        });
    }
}
