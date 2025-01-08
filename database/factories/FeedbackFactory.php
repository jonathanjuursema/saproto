<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
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
            'user_id' => User::query()->inRandomOrder()->first()->id,
            'feedback' => fake()->realTextBetween(50, 200),
            'reviewed' => fake()->boolean(),
            'accepted' => fake()->boolean(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'deleted_at' => fake()->optional(0.1)->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
