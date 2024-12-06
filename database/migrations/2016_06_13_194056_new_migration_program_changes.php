<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class NewMigrationProgramChanges extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function ($table) {
            $table->dropColumn('type');
            $table->dropColumn('fee_cycle');
            $table->dropColumn('primary_member');
        });
        Schema::table('members', function ($table) {
            $table->integer('till')->change();
            $table->integer('since');
            $table->boolean('is_lifelong');
            $table->boolean('is_associate');
            $table->boolean('is_honorary');
            $table->boolean('is_donator');
        });
        Schema::table('users', function ($table) {
            $table->string('phone')->nullable()->default(null)->change();
        });
        Schema::table('bankaccounts', function ($table) {
            $table->boolean('is_first')->default(false);
            $table->dropColumn('withdrawal_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function ($table) {
            $table->date('till')->change();
            $table->boolean('primary_member');
            $table->dropColumn('since');
            $table->dropColumn('is_lifelong');
            $table->dropColumn('is_associate');
            $table->dropColumn('is_honorary');
            $table->dropColumn('is_donator');
        });
        Schema::table('members', function ($table) {
            $table->enum('type', ['ORDINARY', 'ASSOCIATE', 'HONORARY', 'DONATOR']);
            $table->enum('fee_cycle', ['YEARLY', 'FULLTIME']);
        });
        Schema::table('bankaccounts', function ($table) {
            $table->enum('withdrawal_type', ['FRST', 'RCUR']);
            $table->dropColumn('is_first');
        });
    }
}
