<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Committee;
use App\Models\OrderLine;
use App\Models\Product;

/**
 * @extends Factory<OrderLine>
 */
class OrderLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        /** @var Product $product */
        $product = Product::inRandomOrder()->first();
        $date = fake()->dateTimeBetween('-1 year')->format('Y-m-d H:i:s');
        $nbUnits = fake()->randomDigitNotNull();
        $paidCash = fake()->boolean(5);
        if ($paidCash) {
            $tipcie = Committee::find(config('proto.committee')['tipcie']);
            $cashierId = $tipcie->users()->inRandomOrder()->first()->id;
        }

        return [
            'product_id' => $product->id,
            'original_unit_price' => $product->price,
            'units' => $nbUnits,
            'total_price' => $nbUnits * $product->price,
            'created_at' => $date,
            'cashier_id' => $paidCash ? $cashierId : null,
            'payed_with_bank_card' => $paidCash ? $date : null,
            'description' => fake()->sentences(3, true),
        ];
    }
}
