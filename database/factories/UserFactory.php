<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        $firstName = fake()->unique()->firstName($gender);
        $lastName = fake()->lastName();
        $name = "{$firstName} {$lastName}";
        $callingName = explode(' ', $firstName)[0];

        return [
            'name' => $name,
            'calling_name' => $callingName,
            'email' => strtolower(Str::transliterate("{$callingName}.{$lastName}@")).fake()->freeEmailDomain(),
            'password' => bcrypt(Str::random()),
            'remember_token' => Str::random(10),
            'birthdate' => fake()->dateTimeBetween('-40 years', '-16 years')->format('Y-m-d'),
            'phone' => fake()->e164PhoneNumber(),
            'diet' => fake()->sentence(),
            'website' => fake()->url(),
            'phone_visible' => fake()->boolean(),
            'address_visible' => fake()->boolean(),
            'receive_sms' => fake()->boolean(),
            'keep_protube_history' => fake()->boolean(),
            'show_birthday' => fake()->boolean(),
            'show_achievements' => fake()->boolean(),
            'show_omnomcom_total' => fake()->boolean(),
            'show_omnomcom_calories' => fake()->boolean(),
            'disable_omnomcom' => fake()->boolean(),
            'theme' => fake()->randomElement(array_keys(Config::array('proto.themes'))),
            'did_study_create' => fake()->boolean(25),
            'did_study_itech' => fake()->boolean(25),
        ];
    }
}
