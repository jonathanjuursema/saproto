<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudyUsersTableTimeAsInt extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('studies_users', function (Blueprint $table) {
            $table->integer('start');
            $table->dropColumn('till');
            $table->integer('end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studies_users', function (Blueprint $table) {
            $table->dropColumn('start');
            $table->dropColumn('end');
            $table->date('from')->nullable();
        });
    }
}
