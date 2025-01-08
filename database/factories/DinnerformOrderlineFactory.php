<?php

namespace Database\Factories;

use App\Models\Dinnerform;
use App\Models\DinnerformOrderline;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Override;

/**
 * @extends Factory<DinnerformOrderline>
 */
class DinnerformOrderlineFactory extends Factory
{
    protected $model = DinnerformOrderline::class;

    #[Override]
    public function definition(): array
    {
        return [
            'description' => fake()->text(),
            'price' => fake()->randomFloat(),
            'closed' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'helper' => fake()->boolean(),
            'user_id' => User::factory()->has(Member::factory()),
            'dinnerform_id' => Dinnerform::factory(),
        ];
    }
}
