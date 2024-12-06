<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class MoveSecretToEvent extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function ($table) {
            $table->dropColumn('secret');
            $table->dropColumn('active');
        });
        Schema::table('events', function ($table) {
            $table->boolean('secret')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function ($table) {
            $table->boolean('secret')->default(false);
            $table->boolean('active')->default(false);
        });
        Schema::table('events', function ($table) {
            $table->dropColumn('secret');
        });
    }
}
