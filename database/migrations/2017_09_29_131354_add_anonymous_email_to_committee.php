<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddAnonymousEmailToCommittee extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('committees', function ($table) {
            $table->boolean('allow_anonymous_email')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committees', function ($table) {
            $table->dropColumn('allow_anonymous_email');
        });
    }
}
