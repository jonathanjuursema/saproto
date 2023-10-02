<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Member;
use App\Models\User;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $created_at = fake()->dateTimeBetween('2011-04-20')->format('Y-m-d H:i:s');
        $deleted_at = fake()->dateTimeBetween($created_at)->format('Y-m-d H:i:s');

        return [
            'created_at' => $created_at,
            'deleted_at' => fake()->boolean(25) ? $deleted_at : null,
            'user_id' => User::factory()->hasBank()->hasAddress(),
            'proto_username' => fn ($attributes) => Member::createProtoUsername(User::find($attributes['user_id'])->name),
        ];
    }

    /**
     * Indicate that the member is special.
     *
     * @return Factory
     */
    public function special()
    {
        return $this->state(function (array $attributes) {
            $member_types = ['is_lifelong', 'is_honorary', 'is_donor', 'is_pet', 'is_pending'];

            return [
                fake()->randomElement($member_types) => 1,
                'deleted_at' => null,
            ];
        });
    }
}
