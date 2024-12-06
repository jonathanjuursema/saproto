<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNotificationSentToCommitteesActivities extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('committees_activities', function (Blueprint $table) {
            $table->boolean('notification_sent')->after('amount')->default(0);
        });

        DB::table('committees_activities')->where('notification_sent', '=', 0)->update(['notification_sent' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committees_activities', function (Blueprint $table) {
            $table->dropColumn('notification_sent');
        });
    }
}
