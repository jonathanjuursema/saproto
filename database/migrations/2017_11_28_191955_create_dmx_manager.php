<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDmxManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmx_fixtures', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->integer('channel_start');
            $table->integer('channel_end');
        });
        Schema::create('dmx_channel_names', function (Blueprint $table) {
            $table->integer('id');
            $table->text('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dmx_fixtures');
        Schema::drop('dmx_channel_names');
    }
}
