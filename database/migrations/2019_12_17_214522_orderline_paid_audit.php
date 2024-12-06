<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderlinePaidAudit extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orderlines', function (Blueprint $table) {
            $table->text('authenticated_by')->after('total_price')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orderlines', function (Blueprint $table) {
            $table->dropColumn('authenticated_by');
        });
    }
}
