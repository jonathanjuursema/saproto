<?php

use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        if (Schema::hasColumn('events', 'unique_users_count')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('unique_users_count');
            });
        }

        Schema::table('events', function (Blueprint $table) {
            // add a new column that counts the amount of users who signed up called users_count for the event_block number
            $table->unsignedInteger('unique_users_count')->after('publication')->default(0);
        });

        Event::query()->chunk(25, function ($events) {
            foreach ($events as $event) {
                $event->updateUniqueUsersCount();
            }
        });

        // add index to activities_users
        Schema::table('activities_users', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('activity_id');
        });

        // add index to activities.event_id
        Schema::table('activities', function (Blueprint $table) {
            $table->index('event_id');
        });

        // add index to events start and end
        Schema::table('events', function (Blueprint $table) {
            $table->index('start');
            $table->index('end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('unique_users_count');
        });

        Schema::table('activities_users', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['activity_id']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['event_id']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['start']);
            $table->dropIndex(['end']);
        });
    }
};
