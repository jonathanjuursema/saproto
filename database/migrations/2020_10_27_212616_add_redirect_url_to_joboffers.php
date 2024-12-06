<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRedirectUrlToJoboffers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('joboffers', function (Blueprint $table) {
            $table->string('redirect_url')->nullable()->default(null);
            $table->text('description')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joboffers', function (Blueprint $table) {
            $table->dropColumn('redirect_url');
            $table->text('description')->nullable(false)->change();
        });
    }
}
