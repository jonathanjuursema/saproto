<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class Droputwentes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::drop('utwentes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('utwentes', function ($table) {
            $table->increments('id');
            $table->integer('user_id');

            $table->string('organisational_unit');
            $table->string('mail');

            $table->string('room_number')->nullable();
            $table->string('phone_number')->nullable();

            $table->timestamps();
        });
    }
}
