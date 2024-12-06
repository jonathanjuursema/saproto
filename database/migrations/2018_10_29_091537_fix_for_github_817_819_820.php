<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixForGithub817819820 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('show_achievements')->after('show_birthday')->default(true)->nullable(false);
            $table->boolean('keep_omnomcom_history')->after('show_omnomcom_total')->default(true)->nullable(false);
        });
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_educational')->after('is_external')->default(false)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('show_achievements');
            $table->dropColumn('keep_omnomcom_history');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_educational');
        });
    }
}
