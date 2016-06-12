<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function ($table) {
            $table->integer('deregistration_end')->after('registration_end');
        });
        Schema::table('activities_users', function ($table) {
            $table->boolean('withdrawn')->default(false);
            $table->boolean('backup')->default(false);
        });
        Schema::table('events', function ($table) {
            $table->dropColumn('fb_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function ($table) {
            $table->dropColumn('deregistration_end');
        });
        Schema::table('activities_users', function ($table) {
            $table->dropColumn('withdrawn');
            $table->dropColumn('backup');
        });
        Schema::table('events', function ($table) {
            $table->integer('fb_id')->nullable()->default(null);
        });
    }
}
