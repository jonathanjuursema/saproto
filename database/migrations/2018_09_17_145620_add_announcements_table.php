<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable(false);
            $table->text('content')->nullable(false);
            $table->datetime('display_from')->nullable(false);
            $table->datetime('display_till')->nullable(false);
            $table->boolean('show_guests')->nullable(false)->default(false);
            $table->boolean('show_users')->nullable(false)->default(false);
            $table->boolean('show_members')->nullable(false)->default(true);
            $table->boolean('show_only_homepage')->nullable(false)->default(true);
            $table->boolean('show_only_new')->nullable(false)->default(false);
            $table->boolean('show_only_firstyear')->nullable(false)->default(false);
            $table->boolean('show_only_active')->nullable(false)->default(false);
            $table->boolean('show_as_popup')->nullable(false)->default(false);
            $table->tinyInteger('show_style')->nullable(false)->default(0);
            $table->boolean('is_dismissable')->nullable(false)->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('announcements');
    }
}
