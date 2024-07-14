<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddPaymentUrlToMollie extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mollie_transactions', function ($table) {
            $table->string('payment_url')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mollie_transactions', function ($table) {
            $table->dropColumn('payment_url');
        });
    }
}
