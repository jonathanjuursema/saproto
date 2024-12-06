<?php

use App\Models\AchievementOwnership;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlertedColumnToAchievementsUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('achievements_users', function (Blueprint $table) {
            $table->boolean('alerted')->default(false);
        });

        AchievementOwnership::query()->where('alerted', false)->update(['alerted' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achievements_users', function (Blueprint $table) {
            $table->dropColumn('alerted');
        });
    }
}
