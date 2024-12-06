<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class NewsitemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $published_at = fake()->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d H:i:s');

        return [
            'user_id' => User::factory()->create()->id,
            'content' => fake()->paragraphs(3, true),
            'is_weekly' => 0,
            'published_at' => $published_at,
        ];
    }

    public function isWeekly(): Factory|NewsitemFactory
    {
        return $this->state(fn (array $attributes): array => [
            'is_weekly' => 1,
            'published_at' => null,
        ]);
    }
}
