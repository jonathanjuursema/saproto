<?php

namespace Database\Factories;

use App\Models\CodexText;
use App\Models\CodexTextType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Override;

/**
 * @extends Factory<CodexText>
 */
class CodexTextFactory extends Factory
{
    protected $model = CodexText::class;

    #[Override]
    public function definition(): array
    {
        return [
            'type_id' => CodexTextType::factory(),
            'name' => fake()->name(),
            'text' => fake()->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
