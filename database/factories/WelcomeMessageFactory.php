<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WelcomeMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<WelcomeMessage>
 */
class WelcomeMessageFactory extends Factory
{
    protected $model = WelcomeMessage::class;

    public function definition(): array
    {
        return [
            'message' => fake()->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::factory(),
        ];
    }
}
