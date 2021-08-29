<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ArchiveAchievements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('achievement', function (Blueprint $table) {
            $table->boolean('is_archived')->after('tier')->default(false);
            $table->dropColumn('excludeFromAllAchievements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('achievement', function (Blueprint $table) {
            $table->boolean('excludeFromAllAchievements')->nullable(false)->default(false);
            $table->dropColumn('is_archived');
        });
    }
}
