<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWallstreetDrink extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallstreet_drink', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('end_time');
            $table->bigInteger('start_time');
            $table->float('price_increase');
            $table->float('price_decrease');
            $table->float('minimum_price');
            $table->timestamps();
        });

        Schema::create('wallstreet_drink_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallstreet_drink_id');
            $table->foreignId('product_id');
            $table->float('price');
            $table->timestamps();
        });

        //pivot table for many to many relation between products and wallstreet_drink
        Schema::create('product_wallstreet_drink', function (Blueprint $table) {
            $table->foreignId('wallstreet_drink_id');
            $table->foreignId('product_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallstreet_drink');
        Schema::dropIfExists('wallstreet_drink_prices');
        Schema::dropIfExists('product_wallstreet_drink');
    }
}
