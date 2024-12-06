<?php

namespace Database\Factories;

use App\Models\CodexSongCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<SongCategory>
 */
class CodexSongCategoryFactory extends Factory
{
    protected $model = CodexSongCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
