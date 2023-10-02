<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Activity;
use App\Models\ActivityParticipation;

/**
 * @extends Factory<ActivityParticipation>
 */
class ActivityParticipationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'created_at' => fn ($attributes) => self::createAt($attributes),
            'deleted_at' => fn ($attributes) => fake()->boolean(30) ? self::deletedAt($attributes) : null,
            'backup' => fn ($attributes) => self::Backup($attributes),
        ];
    }

    /**
     * Set created at based on activity dates.
     *
     * @return string
     */
    public function createAt(array $attributes)
    {
        $activity = Activity::find($attributes['activity_id']);
        $date = fake()->dateTimeBetween($activity->registration_start, $activity->event->start);

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Set deleted at based on activity dates.
     *
     * @return string
     */
    public function deletedAt(array $attributes)
    {
        $activity = Activity::find($attributes['activity_id']);
        $date = fake()->dateTimeBetween($attributes['created_at'], $activity->event->start);

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Set backup state based on available activity spots.
     *
     * @return bool
     */
    public function backup(array $attributes)
    {
        $activity = Activity::find($attributes['activity_id']);

        return $activity->freeSpots() == 0;
    }
}
