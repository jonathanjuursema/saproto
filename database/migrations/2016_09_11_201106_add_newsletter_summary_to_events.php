<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddNewsletterSummaryToEvents extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function ($table) {
            $table->text('summary')->nullable()->default(null);
        });
        Schema::table('users', function ($table) {
            $table->dropColumn('receive_newsletter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function ($table) {
            $table->dropColumn('summary');
        });
        Schema::table('users', function ($table) {
            $table->boolean('receive_newsletter')->default(false);
        });
    }
}
