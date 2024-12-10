<?php

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
        Schema::table('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->change();
            $table->string('ip_address', 45)->nullable()->change();
            $table->text('user_agent')->nullable()->change();
            $table->longText('payload')->change();
            $table->integer('last_activity')->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->string('id')->unique()->change();
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->string('ip_address', 45)->nullable()->change();
            $table->text('user_agent')->nullable()->change();
            $table->text('payload')->change();
            $table->integer('last_activity')->change();
        });
    }
};
