<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_categories', function (Blueprint $table) {
            $table->boolean('show_publisher')->after('reviewer_id')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('feedback_categories', function (Blueprint $table) {
            $table->dropColumn('show_publisher');
        });
    }
};
