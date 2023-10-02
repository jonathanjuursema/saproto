<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\HashMapItem;

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
        return $this->state(function (array $attributes) {
            return [
                'value' => fake()->realText(800),
            ];
        });
    }

    /**
     * Create a HashMapItem with current date.
     *
     * @return Factory
     */
    public function date()
    {
        return $this->state(function (array $attributes) {
            return [
                'value' => date('U'),
            ];
        });
    }
}
