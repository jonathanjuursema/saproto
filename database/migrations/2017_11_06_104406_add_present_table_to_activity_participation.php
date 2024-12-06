<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPresentTableToActivityParticipation extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities_users', function (Blueprint $table) {
            $table->boolean('is_present')->after('user_id')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities_users', function (Blueprint $table) {
            $table->dropColumn('is_present');
        });
    }
}
