<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAchievementTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievement', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('desc');
            $table->integer('img_file_id');
            $table->string('tier');
            $table->timestamps();
        });

        Schema::create('achievements_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('achievement_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('achievement');
        Schema::drop('achievements_users');
    }
}
