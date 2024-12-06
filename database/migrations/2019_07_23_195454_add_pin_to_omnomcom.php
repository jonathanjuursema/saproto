<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPinToOmnomcom extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orderlines', function (Blueprint $table) {
            $table->datetime('payed_with_bank_card')->after('payed_with_cash')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orderlines', function (Blueprint $table) {
            $table->dropColumn('payed_with_bank_card');
        });
    }
}
