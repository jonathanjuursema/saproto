<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\AchievementOwnership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AchievementOwnership>
 */
class AchievementOwnershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-1 year')->format('Y-m-d H:i:s');

        return [
            'achievement_id' => Achievement::query()->inRandomOrder()->first()->id,
            'created_at' => $date,
            'updated_at' => $date,
            'alerted' => fake()->boolean(),
        ];
    }
}
