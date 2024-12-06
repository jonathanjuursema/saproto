<?php

use App\Models\ProductCategoryEntry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddProductTierToCategory extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products_categories', function ($table) {
            $table->unsignedInteger('rank')->nullable(false)->default(1);
        });

        $rows = ProductCategoryEntry::all();
        foreach ($rows as $row) {
            $row->rank = $row->id;
            $row->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_categories', function ($table) {
            $table->dropColumn('rank');
        });
    }
}
