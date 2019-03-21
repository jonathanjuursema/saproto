<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Updateactivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->integer('organizing_commitee')->nullable()->default(null);
        });
        Schema::table('committees_events', function(Blueprint $table) {
           $table->renameColumn('event_id', 'activity_id');
        });
        Schema::rename('committees_events', 'committees_activities');
        Schema::table('activities_users', function(Blueprint $table) {
            $table->renameColumn('committees_events_id', 'committees_activities_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('organizing_commitee');
        });
        Schema::table('committees_activities', function(Blueprint $table) {
            $table->renameColumn('activity_id', 'event_id');
        });
        Schema::rename('committees_activities', 'committees_events');
        Schema::table('activities_users', function(Blueprint $table) {
            $table->renameColumn('committees_activities_id', 'committees_events_id');
        });
    }
}
