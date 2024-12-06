<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderboardsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('committee_id');
            $table->string('name');
            $table->string('description');
            $table->string('icon')->nullable();
            $table->string('points_name')->default('points');
            $table->timestamps();
        });

        Schema::create('leaderboards_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leaderboard_id');
            $table->integer('user_id');
            $table->integer('points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
        Schema::dropIfExists('leaderboards_entries');
    }
}
