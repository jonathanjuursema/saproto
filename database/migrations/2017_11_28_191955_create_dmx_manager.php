<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmxManager extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
     */
    public function down(): void
    {
        Schema::drop('dmx_fixtures');
        Schema::drop('dmx_channel_names');
    }
}
