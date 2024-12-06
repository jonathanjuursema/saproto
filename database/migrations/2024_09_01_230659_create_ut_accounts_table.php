<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ut_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id');
            $table->string('number');
            $table->string('mail');
            $table->string('department')->nullable();
            $table->string('givenname');
            $table->string('surname');
            $table->boolean('found')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ut_accounts');
    }
};
