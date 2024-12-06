<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntroducingSoftdelete extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities_users', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropColumn('withdrawn');
        });
        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('committees_users', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropColumn('start');
            $table->dropColumn('end');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('members', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropColumn('till');
            $table->dropColumn('since');
        });
        Schema::table('studies_users', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropColumn('start');
            $table->dropColumn('end');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities_users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->boolean('withdrawn');
        });
        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('committees_users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->integer('start');
            $table->integer('end')->nullable()->default(null);
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->integer('since');
            $table->integer('till')->nullable()->default(null);
        });
        Schema::table('studies_users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->integer('start');
            $table->integer('end')->nullable()->default(null);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
