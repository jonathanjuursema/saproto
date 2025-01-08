<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WelcomeMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Override;

/**
 * @extends Factory<WelcomeMessage>
 */
class WelcomeMessageFactory extends Factory
{
    protected $model = WelcomeMessage::class;

    #[Override]
    public function definition(): array
    {
        return [
            'message' => fake()->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::factory(),
        ];
    }
}
