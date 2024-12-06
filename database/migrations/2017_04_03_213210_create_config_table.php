<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hashmap', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->nullable(false);
            $table->string('subkey')->nullable(true)->default(null);
            $table->string('value')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('hashmap');
    }
}
