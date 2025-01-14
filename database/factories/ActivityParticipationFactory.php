<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityParticipation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

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
    #[Override]
    public function definition(): array
    {
        return [
            'created_at' => fn ($attributes): string => self::createAt($attributes),
            'deleted_at' => fn ($attributes): ?string => fake()->boolean(30) ? self::deletedAt($attributes) : null,
            'backup' => fn ($attributes): bool => self::Backup($attributes),
        ];
    }

    /**
     * Set created at based on activity dates.
     */
    public function createAt(array $attributes): string
    {
        $activity = Activity::query()->find($attributes['activity_id']);

        $start = Carbon::parse($activity->registration_start);
        $end = Carbon::parse($activity->event->start);

        $date = fake()->dateTimeBetween($start, $end);

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Set deleted at based on activity dates.
     */
    public function deletedAt(array $attributes): string
    {
        $activity = Activity::query()->find($attributes['activity_id']);

        $start = Carbon::parse($attributes['created_at']);
        $end = Carbon::parse($activity->event->start);

        $date = fake()->dateTimeBetween($start, $end);

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Set backup state based on available activity spots.
     */
    public function backup(array $attributes): bool
    {
        $activity = Activity::query()->find($attributes['activity_id']);

        return $activity->freeSpots() == 0;
    }
}
