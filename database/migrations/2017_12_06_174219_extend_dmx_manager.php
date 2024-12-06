<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendDmxManager extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
     */
    public function down(): void
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
