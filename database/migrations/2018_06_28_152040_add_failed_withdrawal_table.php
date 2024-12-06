<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFailedWithdrawalTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('withdrawals_failed', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('withdrawal_id');
            $table->integer('user_id');
            $table->integer('correction_orderline_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('withdrawals_failed');
    }
}
