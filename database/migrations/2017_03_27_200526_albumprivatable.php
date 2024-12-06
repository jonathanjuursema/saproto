<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class Albumprivatable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('flickr_albums', function ($table) {
            $table->boolean('private')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flickr_albums', function ($table) {
            $table->dropColumn('private');
        });
    }
}
