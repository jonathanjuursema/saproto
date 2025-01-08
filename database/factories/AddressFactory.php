<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function definition()
    {
        return [
            'street' => fake()->streetName,
            'number' => fake()->buildingNumber,
            'zipcode' => fake()->postcode,
            'city' => fake()->city,
            'country' => fake()->country,
        ];
    }
}
