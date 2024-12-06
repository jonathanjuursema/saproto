<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewPhotoTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('photo_likes', 'flickr_likes');

        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('file_id');
            $table->integer('album_id');
            $table->integer('date_taken');
            $table->boolean('private');
        });

        Schema::create('photo_albums', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->char('name', 255);
            $table->integer('date_create');
            $table->integer('date_taken');
            $table->integer('thumb_id');
            $table->integer('event_id')->nullable()->default(null);
            $table->boolean('private');
            $table->boolean('published')->default(false);
        });

        Schema::create('photo_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('photo_id');
            $table->integer('user_id');
        });

        Schema::table('flickr_albums', function (Blueprint $table) {
            $table->boolean('migrated')->default(false);
        });

        Schema::table('flickr_items', function (Blueprint $table) {
            $table->boolean('migrated')->default(false);
        });

        Schema::table('flickr_likes', function (Blueprint $table) {
            $table->boolean('migrated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flickr_albums', function (Blueprint $table) {
            $table->dropColumn('migrated');
        });

        Schema::table('flickr_items', function (Blueprint $table) {
            $table->dropColumn('migrated');
        });

        Schema::table('flickr_likes', function (Blueprint $table) {
            $table->dropColumn('migrated');
        });

        Schema::dropIfExists('photo_likes');
        Schema::dropIfExists('photo_albums');
        Schema::dropIfExists('photos');

        Schema::rename('flickr_likes', 'photo_likes');
    }
}
