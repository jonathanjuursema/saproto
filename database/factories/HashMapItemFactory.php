<?php

namespace Database\Factories;

use App\Models\HashMapItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HashMapItem>
 */
class HashMapItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'key' => 'key',
            'value' => null,
        ];
    }

    /**
     * Create a HashMapItem with text.
     *
     * @return Factory
     */
    public function text()
    {
        return $this->state(fn (array $attributes): array => [
            'value' => fake()->realText(800),
        ]);
    }

    /**
     * Create a HashMapItem with current date.
     *
     * @return Factory
     */
    public function date()
    {
        return $this->state(fn (array $attributes): array => [
            'value' => date('U'),
        ]);
    }
}
