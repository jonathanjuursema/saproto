<?php

namespace Database\Factories;

use App\Models\Codex;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Codex>
 */
class CodexFactory extends Factory
{
    protected $model = Codex::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'export' => fake()->word(),
            'description' => fake()->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
