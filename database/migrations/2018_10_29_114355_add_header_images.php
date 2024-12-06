<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeaderImages extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('headerimages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable(false);
            $table->integer('credit_id')->nullable(true);
            $table->integer('image_id')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('headerimages');
    }
}
