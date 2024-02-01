<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'text' => fake()->text(2000),
            'preview_img' => fake()->filePath(),
        ];
    }

    public function withPreviewImg(string $previewImg): static
    {
        return $this->state(fn () => ['preview_img' => $previewImg]);
    }
}
