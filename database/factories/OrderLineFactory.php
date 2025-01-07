<?php

namespace Database\Factories;

use App\Models\Committee;
use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;
use Override;

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
    #[Override]
    public function definition(): array
    {
        /** @var Product $product */
        $product = Product::query()->inRandomOrder()->first();
        $date = fake()->dateTimeBetween('-1 year')->format('Y-m-d H:i:s');
        $nbUnits = fake()->randomDigitNotNull();
        $paidCash = fake()->boolean(5);
        if ($paidCash) {
            $tipcie = Committee::query()->find(Config::integer('proto.committee.tipcie'));
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
