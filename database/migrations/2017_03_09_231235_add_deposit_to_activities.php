<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddDepositToActivities extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function ($table) {
            $table->float('no_show_fee', 8, 2)->after('price')->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function ($table) {
            $table->dropColumn('no_show_fee');
        });
    }
}
