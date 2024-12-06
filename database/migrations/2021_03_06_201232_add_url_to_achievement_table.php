<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToAchievementTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('achievement', function (Blueprint $table) {
            $table->boolean('has_page')->after('tier');
            $table->string('page_name')->nullable()->unique()->after('has_page');
            $table->text('page_content')->nullable()->after('page_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achievement', function (Blueprint $table) {
            $table->dropColumn('has_page');
            $table->dropColumn('page_name');
            $table->dropColumn('page_content');
        });
    }
}
