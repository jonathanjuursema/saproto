<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlayedvideosAddSpotifyid extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('playedvideos', function (Blueprint $table) {
            $table->string('spotify_id', 50)->after('video_title')->nullable()->default(null);
            $table->text('spotify_name')->after('spotify_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('playedvideos', function (Blueprint $table) {
            $table->dropColumn('spotify_id');
            $table->dropColumn('spotify_name');
        });
    }
}
