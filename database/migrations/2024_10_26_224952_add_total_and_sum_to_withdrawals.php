<?php

use App\Models\Withdrawal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->integer('total_users_associated')->default(0);
            $table->integer('total_orderlines_associated')->default(0);
            $table->decimal('sum_associated_orderlines', 10)->default(0);
        });

        foreach (Withdrawal::all('id') as $withdrawal) {
            $withdrawal->recalculateTotals();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn('total_users_associated');
            $table->dropColumn('total_orderlines_associated');
            $table->dropColumn('sum_associated_orderlines');
        });
    }
};
