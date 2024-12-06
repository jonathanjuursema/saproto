<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDinnerOrderlineTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dinnerform_orderline', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('dinnerform_id');
            $table->text('description');
            $table->float('price');
            $table->boolean('helper');
            $table->boolean('closed');
        });

        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->integer('event_id')->nullable();
            $table->integer('discount')->nullable();
            $table->boolean('closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinnerform_orderline');
        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->dropColumn('discount');
            $table->dropColumn('closed');
        });
    }
}
