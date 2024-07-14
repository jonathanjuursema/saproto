<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AllowNullUserOrderline extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orderlines', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orderlines', function (Blueprint $table) {
            $table->integer('user_id')->nullable(false)->change();
        });
    }
}
