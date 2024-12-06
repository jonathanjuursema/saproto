<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoSupportNarrowcasting extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('narrowcasting', function (Blueprint $table) {
            $table->string('youtube_id')->nullable()->default(null);
            $table->integer('image_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('narrowcasting', function (Blueprint $table) {
            $table->dropColumn('youtube_id');
            $table->integer('image_id')->nullable(false)->change();
        });
    }
}
