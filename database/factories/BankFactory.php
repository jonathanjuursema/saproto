<?php

namespace Database\Factories;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Bank>
 */
class BankFactory extends Factory
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
            'iban' => fake()->iban(),
            'bic' => fake()->swiftBicNumber,
            'machtigingid' => 'PROTOX'.fake()->randomNumber(5, true).'X'.fake()->randomNumber(5, true),
        ];
    }
}
