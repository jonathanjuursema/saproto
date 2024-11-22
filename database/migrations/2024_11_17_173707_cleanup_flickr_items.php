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
        Schema::dropIfExists('flickr_items');
        Schema::dropIfExists('flickr_albums');
        Schema::dropIfExists('flickr_likes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('flickr_items', function (Blueprint $table) {
            $table->string('url')->primary();
            $table->string('thumb_id');
            $table->string('album_id');
            $table->string('id');
            $table->integer('date_taken');
            $table->boolean('private')->default(false);
            $table->boolean('migrated')->default(false);
        });

        Schema::create('flickr_albums', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('thumb');
            $table->integer('date_create');
            $table->integer('date_update');
            $table->integer('date_taken')->nullable();
            $table->integer('event_id')->nullable();
            $table->boolean('private')->default(false);
            $table->boolean('migrated')->default(false);
        });

        Schema::create('flickr_likes', function (Blueprint $table) {
            $table->bigInteger('photo_id');
            $table->integer('user_id');
            $table->timestamps();
            $table->boolean('migrated')->default(false);
        });
    }
};
