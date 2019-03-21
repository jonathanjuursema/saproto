<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtendDmxManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('dmx_channel_names', 'dmx_channels');
        Schema::table('dmx_fixtures', function (Blueprint $table) {
            $table->boolean('follow_timetable')->default(false);
        });
        Schema::table('dmx_channels', function (Blueprint $table) {
            $table->char('special_function', 10)->default('none');
        });
        Schema::create('dmx_overrides', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fixtures');
            $table->string('color');
            $table->string('start');
            $table->string('end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dmx_fixtures', function (Blueprint $table) {
            $table->dropColumn('follow_timetable');
        });
        Schema::table('dmx_channels', function (Blueprint $table) {
            $table->dropColumn('special_function');
        });
        Schema::drop('dmx_overrides');
        Schema::rename('dmx_channels', 'dmx_channel_names');
    }
}
