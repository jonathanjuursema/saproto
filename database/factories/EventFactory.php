<?php

namespace Database\Factories;

use App\Models\Committee;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\StorageEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'description' => fake()->text(),
            'start' => fake()->dateTimeBetween('-1 week', 'now')->getTimestamp(),
            'end' => fake()->dateTimeBetween('now', '+1 week')->getTimestamp(),
            'publication' => null,
            'summary' => fake()->text(),
            'location' => fake()->word(),
            'is_featured' => fake()->boolean(),
            'is_external' => fake()->boolean(),
            'involves_food' => fake()->boolean(),
            'secret' => fake()->boolean(),
            'force_calendar_sync' => fake()->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'unique_users_count' => fake()->randomNumber(),
            'image_id' => StorageEntry::factory(),
            'committee_id' => Committee::factory(),
            'category_id' => EventCategory::factory(),
        ];
    }
}
