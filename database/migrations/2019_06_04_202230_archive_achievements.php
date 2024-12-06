<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ArchiveAchievements extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('achievement', function (Blueprint $table) {
            $table->boolean('is_archived')->after('tier')->default(false);
            $table->dropColumn('excludeFromAllAchievements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achievement', function (Blueprint $table) {
            $table->boolean('excludeFromAllAchievements')->nullable(false)->default(false);
            $table->dropColumn('is_archived');
        });
    }
}
