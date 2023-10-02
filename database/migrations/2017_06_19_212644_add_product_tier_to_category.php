<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ProductCategoryEntry;

class AddProductTierToCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_categories', function ($table) {
            $table->dropColumn('rank');
        });
    }
}
