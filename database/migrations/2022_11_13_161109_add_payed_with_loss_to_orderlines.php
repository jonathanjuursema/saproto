<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayedWithLossToOrderlines extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orderlines', function (Blueprint $table) {
            $table->boolean('payed_with_loss')->after('payed_with_withdrawal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orderlines', function (Blueprint $table) {
            $table->dropColumn('payed_with_loss');
        });
    }
}
