<?php

namespace Database\Factories;

use App\Models\Committee;
use App\Models\StorageEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Committee>
 */
class CommitteeFactory extends Factory
{
    protected $model = Committee::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'description' => fake()->text(),
            'public' => fake()->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'allow_anonymous_email' => fake()->unique()->safeEmail(),
            'is_society' => fake()->boolean(),
            'is_active' => fake()->boolean(),
            'image_id' => StorageEntry::factory(),
        ];
    }
}
