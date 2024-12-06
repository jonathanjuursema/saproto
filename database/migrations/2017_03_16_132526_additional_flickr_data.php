<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AdditionalFlickrData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('flickr_items', function ($table) {
            $table->string('id');
            $table->integer('date_taken');
        });

        Schema::table('flickr_albums', function ($table) {
            $table->integer('date_taken')->nullable()->default(null);
            $table->integer('event_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flickr_items', function ($table) {
            $table->dropColumn('id');
            $table->dropColumn('date_taken');
        });

        Schema::table('flickr_albums', function ($table) {
            $table->dropColumn('date_taken');
            $table->dropColumn('event_id');
        });
    }
}
