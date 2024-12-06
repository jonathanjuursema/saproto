<?php

use App\Models\ProductCategoryEntry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderOmnomcom extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products_categories', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_categories', function ($table) {
            $table->unsignedInteger('rank')->nullable(false)->default(0);
        });
        $rows = ProductCategoryEntry::all();
        foreach ($rows as $row) {
            $row->rank = $row->id;
            $row->save();
        }
    }
}
