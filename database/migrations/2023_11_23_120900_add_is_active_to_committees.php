<?php

use App\Models\Committee;
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
        // Create new is_active column to show if society is active or not
        Schema::table('committees', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        // Set all hidden committees to inactive
        Committee::query()->where('public', 0)->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the is_active column
        Schema::table('committees', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
