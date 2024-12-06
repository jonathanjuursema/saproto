<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable(false);
            $table->integer('event_id')->nullable(true)->default(null);
            $table->string('youtube_id')->nullable(false);
            $table->string('youtube_title')->nullable(false);
            $table->string('youtube_length')->nullable(false);
            $table->string('youtube_user_id')->nullable(false);
            $table->string('youtube_user_name')->nullable(false);
            $table->string('youtube_thumb_url')->nullable(false);
            $table->date('video_date')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('videos');
    }
}
