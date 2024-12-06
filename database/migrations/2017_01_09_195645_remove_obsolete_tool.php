<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveObsoleteTool extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::drop('pastries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('pastries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id_a');
            $table->integer('user_id_b')->nullable();
            $table->string('person_b')->nullable();
            $table->integer('pastry');
            $table->timestamps();
        });
    }
}
