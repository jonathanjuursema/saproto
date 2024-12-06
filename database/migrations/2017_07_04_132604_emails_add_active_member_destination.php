<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class EmailsAddActiveMemberDestination extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emails', function ($table) {
            $table->boolean('to_active')->default(false)->after('to_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function ($table) {
            $table->dropColumn('to_active');
        });
    }
}
