<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrauthRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qrauth_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('auth_token');
            $table->text('qr_token');
            $table->text('description');
            $table->datetime('approved_at')->nullable(true)->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('qrauth_requests');
    }
}
