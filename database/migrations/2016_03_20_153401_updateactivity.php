<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Updateactivity extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->integer('organizing_commitee')->nullable()->default(null);
        });
        Schema::table('committees_events', function (Blueprint $table) {
            $table->renameColumn('event_id', 'activity_id');
        });
        Schema::rename('committees_events', 'committees_activities');
        Schema::table('activities_users', function (Blueprint $table) {
            $table->renameColumn('committees_events_id', 'committees_activities_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('organizing_commitee');
        });
        Schema::table('committees_activities', function (Blueprint $table) {
            $table->renameColumn('activity_id', 'event_id');
        });
        Schema::rename('committees_activities', 'committees_events');
        Schema::table('activities_users', function (Blueprint $table) {
            $table->renameColumn('committees_activities_id', 'committees_events_id');
        });
    }
}
