<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddMembercardAttributesToCompanies extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function ($table) {
            $table->boolean('on_membercard')->default(false);
            $table->text('membercard_excerpt')->nullable();
            $table->text('membercard_long')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function ($table) {
            $table->dropColumn('on_membercard');
            $table->dropColumn('membercard_excerpt');
            $table->dropColumn('membercard_long');
        });
    }
}
