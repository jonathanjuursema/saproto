<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\PhotoAlbum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Override;

/**
 * @extends Factory<PhotoAlbum>
 */
class PhotoAlbumFactory extends Factory
{
    protected $model = PhotoAlbum::class;

    #[Override]
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'date_create' => fake()->randomNumber(),
            'date_taken' => fake()->randomNumber(),
            'thumb_id' => 0,
            'private' => fake()->boolean(),
            'published' => fake()->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'event_id' => Event::factory(),
        ];
    }
}
