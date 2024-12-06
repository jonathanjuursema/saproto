<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShortUrl extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('short_url', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable(false);
            $table->string('url')->nullable(false)->unique();
            $table->string('target')->nullable(false);
            $table->integer('clicks')->nullable(false)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('short_url');
    }
}
