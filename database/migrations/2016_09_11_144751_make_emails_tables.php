<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeEmailsTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('subject');
            $table->string('sender_name');
            $table->string('sender_address');
            $table->text('body');
            $table->boolean('to_user')->default(false);
            $table->boolean('to_member')->default(false);
            $table->boolean('to_list')->default(false);
            $table->integer('sent_to')->nullable()->default(null);
            $table->boolean('sent')->default(false);
            $table->boolean('ready')->default(false);
            $table->integer('time');
            $table->timestamps();
        });
        Schema::create('emails_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_id');
            $table->integer('list_id');
        });
        Schema::create('emails_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_id');
            $table->integer('file_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('emails');
        Schema::drop('emails_files');
        Schema::drop('emails_lists');
    }
}
