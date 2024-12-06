<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventTicketsTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id');
            $table->integer('product_id');
            $table->boolean('members_only');
            $table->integer('available_from');
            $table->integer('available_to');
        });
        Schema::create('ticket_purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id');
            $table->integer('orderline_id');
            $table->integer('user_id');
            $table->string('barcode');
            $table->datetime('scanned')->nullable(true)->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tickets');
        Schema::drop('ticket_purchases');
    }
}
