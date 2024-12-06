<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNarrowcastingTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('narrowcasting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('image_id');
            $table->integer('campaign_start');
            $table->integer('campaign_end');
            $table->integer('slide_duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('narrowcasting');
    }
}
