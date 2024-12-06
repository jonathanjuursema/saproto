<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveSettingsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('pref_calendar_alarm')->after('keep_protube_history')->nullable(true)->default(null);
            $table->boolean('pref_calendar_relevant_only')->after('pref_calendar_alarm')->nullable(false)->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pref_calendar_alarm');
            $table->dropColumn('pref_calendar_relevant_only');
        });
    }
}
