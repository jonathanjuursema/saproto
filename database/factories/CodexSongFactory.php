<?php

namespace Database\Factories;

use App\Models\CodexSong;
use App\Models\CodexSongCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Override;

/**
 * @extends Factory<CodexSong>
 */
class CodexSongFactory extends Factory
{
    protected $model = CodexSong::class;

    #[Override]
    public function definition(): array
    {
        return [
            'category_id' => CodexSongCategory::factory(),
            'title' => fake()->word(),
            'artist' => fake()->word(),
            'lyrics' => fake()->word(),
            'youtube' => fake()->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
