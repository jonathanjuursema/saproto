<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DefaultMembershipTypes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->boolean('is_lifelong')->nullable(false)->default(false)->change();
            $table->boolean('is_honorary')->nullable(false)->default(false)->change();
            $table->boolean('is_donator')->nullable(false)->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->boolean('is_lifelong')->nullable()->default(null)->change();
            $table->boolean('is_honorary')->nullable()->default(null)->change();
            $table->boolean('is_donator')->nullable()->default(null)->change();
        });
    }
}
