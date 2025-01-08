<?php

namespace Database\Factories;

use App\Models\StorageEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Override;

/**
 * @extends Factory<StorageEntry>
 */
class StorageEntryFactory extends Factory
{
    protected $model = StorageEntry::class;

    #[Override]
    public function definition(): array
    {
        return [
            'filename' => fake()->word(),
            'mime' => fake()->word(),
            'original_filename' => fake()->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'hash' => fake()->word(),
        ];
    }
}
