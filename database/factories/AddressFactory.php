<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Address;

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
