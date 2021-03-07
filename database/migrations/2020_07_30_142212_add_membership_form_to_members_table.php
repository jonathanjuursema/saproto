<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMembershipFormToMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function(Blueprint $table) {
            $table->string('membership_form_id')->after('proto_username')->nullable()->default(null);
            $table->string('pending')->after('is_donator')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('membership_form_id');
            $table->dropColumn('pending');
        });
    }
}
