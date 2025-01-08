<?php

namespace Database\Factories;

use App\Models\HeaderImage;
use App\Models\StorageEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Override;

/**
 * @extends Factory<HeaderImage>
 */
class HeaderImageFactory extends Factory
{
    protected $model = HeaderImage::class;

    #[Override]
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'credit_id' => User::factory(),
            'image_id' => StorageEntry::factory(),
        ];
    }
}
