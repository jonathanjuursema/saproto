<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Committees extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('slug');
            $table->text('description');

            $table->boolean('public')->default(true);

            $table->timestamps();
        });

        Schema::create('committees_users', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->integer('committee_id');

            $table->string('role')->nullable();
            $table->string('edition')->nullable();

            $table->date('start');
            $table->date('end')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('committees');
        Schema::drop('committees_users');
    }
}
