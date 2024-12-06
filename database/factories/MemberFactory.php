<?php

namespace Database\Factories;

use App\Enums\MembershipTypeEnum;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
    public function definition(): array
    {
        $created_at = fake()->dateTimeBetween('2011-04-20')->format('Y-m-d H:i:s');

        return [
            'created_at' => $created_at,
            'deleted_at' => null,
            'user_id' => User::factory()->hasBank()->hasAddress(),
            'proto_username' => fn ($attributes): string => Member::createProtoUsername(User::query()->find($attributes['user_id'])->name),
            'membership_type' => MembershipTypeEnum::REGULAR,
        ];
    }

    /**
     * Indicate that the member is special.
     */
    public function special(): Factory
    {
        return $this->state(function (array $attributes): array {
            $member_types = [MembershipTypeEnum::DONOR, MembershipTypeEnum::HONORARY, MembershipTypeEnum::LIFELONG];

            return [
                fake()->randomElement($member_types) => 1,
                'deleted_at' => null,
            ];
        });
    }
}
