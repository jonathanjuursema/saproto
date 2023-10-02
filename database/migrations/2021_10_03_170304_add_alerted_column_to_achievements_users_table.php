<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AchievementOwnership;

class AddAlertedColumnToAchievementsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('achievements_users', function (Blueprint $table) {
            $table->boolean('alerted')->default(false);
        });

        AchievementOwnership::where('alerted', false)->update(['alerted' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('achievements_users', function (Blueprint $table) {
            $table->dropColumn('alerted');
        });
    }
}
