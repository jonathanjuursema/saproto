<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Override;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    #[Override]
    public function definition(): array
    {
        return [
            'price' => fake()->randomFloat(),
            'no_show_fee' => fake()->randomFloat(),
            'participants' => fake()->randomNumber(),
            'attendees' => null,
            'registration_start' => Carbon::now(),
            'registration_end' => Carbon::now()->addDay(),
            'deregistration_end' => Carbon::now()->addHour(),
            'comment' => fake()->word(),
            'redirect_url' => null,
            'closed' => false,
            'hide_participants' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'event_id' => Event::factory(),
        ];
    }
}
