<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //add an int category_id column to codex_songs
        Schema::table('codex_songs', function (Blueprint $table) {
            $table->foreignId('category_id')->after('id');
        });

        //get the rows from the codex_category_song table and insert them into the new category_id column
        $songs = DB::table('codex_category_song')->get();
        foreach ($songs as $song) {
            DB::table('codex_songs')->where('id', $song->song)->update(['category_id' => $song->category]);
        }

        Schema::table('codex_codex_song', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('codex_codex_text', function (Blueprint $table) {
            $table->dropColumn('text_index');
        });

        //remove the codex_category_song table
        Schema::dropIfExists('codex_category_song');

        //remove the codex_codexshuffle table
        Schema::dropIfExists('codex_codexshuffle');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //reverse the changes
        Schema::create('codex_category_song', function (Blueprint $table) {
            $table->integer('song');
            $table->integer('category');
        });

        Schema::table('codex_codex_song', function (Blueprint $table) {
            $table->integer('category')->unsigned();
        });

        Schema::table('codex_codex_text', function (Blueprint $table) {
            $table->integer('text_index')->unsigned();
        });

        $songs = DB::table('codex_songs')->get();
        foreach ($songs as $song) {
            DB::table('codex_category_song')->insert(['song' => $song->id, 'category' => $song->category_id]);
        }

        Schema::table('codex_songs', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });

        Schema::create('codex_codexshuffle', function (Blueprint $table) {
            $table->id();
            $table->integer('codex')->unsigned();
            $table->integer('category')->unsigned();
            $table->timestamps();
        });
    }
};
