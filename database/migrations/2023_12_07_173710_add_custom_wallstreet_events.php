<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CREATE NEW TABLE CALLED wallstreet_drink_events with id, name and percentage
        Schema::create('wallstreet_drink_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('percentage');
            $table->text('description')->nullable();
            $table->integer('image_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('wallstreet_drink_event', function (Blueprint $table) {
            $table->id();
            $table->integer('wallstreet_drink_id')->unsigned();
            $table->integer('wallstreet_drink_events_id')->unsigned();
            $table->timestamps();
        });

        // create a new table called wallstreet_drink_event_product with id, wallstreet_drink_event_id and product_id
        Schema::create('wallstreet_drink_event_product', function (Blueprint $table) {
            $table->id();
            $table->integer('wallstreet_drink_event_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('wallstreet_drink', function (Blueprint $table) {
            $table->boolean('random_events')->default(true)->after('minimum_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // revert up
        Schema::dropIfExists('wallstreet_drink_events');
        Schema::dropIfExists('wallstreet_drink_event_product');
        Schema::dropIfExists('wallstreet_drink_event');
        Schema::table('wallstreet_drink', function (Blueprint $table) {
            $table->dropColumn('random_events');
        });
    }
};
