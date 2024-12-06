<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->unique()->words(fake()->numberBetween(1, 3), true),
            'slug' => fn ($attributes) => Str::slug($attributes['title']),
            'content' => fake()->paragraphs(fake()->numberBetween(5, 15), true),
            'is_member_only' => fake()->boolean(),
            'featured_image_id' => null,
            'show_attachments' => false,
        ];
    }
}
