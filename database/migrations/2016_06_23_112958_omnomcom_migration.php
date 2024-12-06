<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OmnomcomMigration extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('display')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('products_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('category_id');
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->integer('image_id')->nullable()->default(null);
            $table->string('name');
            $table->string('nicename');
            $table->float('price');
            $table->integer('stock')->default(0);
            $table->integer('preferred_stock')->default(0);
            $table->integer('max_stock')->default(0);
            $table->integer('supplier_collo')->default(0);
            $table->boolean('is_visible')->default(false);
            $table->boolean('is_alcoholic')->default(false);
            $table->boolean('is_visible_when_no_stock')->default(false);
            $table->timestamps();
        });
        Schema::create('orderlines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('cashier_id')->nullable()->default(null);
            $table->integer('product_id');
            $table->float('original_unit_price');
            $table->integer('units');
            $table->float('total_price');
            $table->datetime('payed_with_cash')->nullable()->default(null);
            $table->integer('payed_with_mollie')->nullable()->default(null);
            $table->integer('payed_with_withdrawal')->nullable()->default(null);
            $table->timestamps();
        });
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->timestamps();
        });
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_number');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('product_categories');
        Schema::drop('products_categories');
        Schema::drop('products');
        Schema::drop('orderlines');
        Schema::drop('withdrawals');
        Schema::drop('accounts');
    }
}
