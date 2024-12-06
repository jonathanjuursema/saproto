<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RegistrationMigration extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->default(null)->change();
            $table->boolean('phone_visible')->default(false)->change();
            $table->boolean('address_visible')->default(false)->change();
            $table->boolean('receive_newsletter')->default(false)->change();
            $table->boolean('receive_sms')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->default('')->change();
            $table->boolean('phone_visible')->default(true)->change();
            $table->boolean('address_visible')->default(true)->change();
            $table->boolean('receive_newsletter')->default(true)->change();
            $table->boolean('receive_sms')->default(true)->change();
        });
    }
}
