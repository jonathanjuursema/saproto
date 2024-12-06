<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddDietToUser extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function ($table) {
            $table->text('diet')->after('phone')->nullable()->default(null);
        });
        Schema::table('events', function ($table) {
            $table->boolean('involves_food')->after('location')->nullable(false)->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('diet');
        });
        Schema::table('events', function ($table) {
            $table->dropColumn('involves_food');
        });
    }
}
