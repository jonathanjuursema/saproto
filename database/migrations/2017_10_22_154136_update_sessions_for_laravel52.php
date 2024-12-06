<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateSessionsForLaravel52 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sessions', function ($table) {
            $table->integer('user_id')->nullable(true);
            $table->string('ip_address')->nullable(true);
            $table->text('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function ($table) {
            $table->dropColumn('user_id');
            $table->dropColumn('ip_address');
            $table->dropColumn('user_agent');
        });
    }
}
