<?php

namespace Database\Factories;

use App\Models\CommitteeMembership;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<CommitteeMembership>
 */
class CommitteeMembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function definition()
    {
        $created_at = fake()->dateTimeBetween('-1 year', '+1 year');
        $deleted_at = fake()->dateTimeBetween($created_at, '+1 year');

        return [
            'role' => 'Automatically Added',
            'edition' => fake()->boolean() ? fake()->randomDigitNotNull() : null,
            'created_at' => $created_at,
            'deleted_at' => fake()->boolean(30) ? $deleted_at : null,
        ];
    }
}
