<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public final const string DEFAULT_PASSWORD = 'password';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make(static::DEFAULT_PASSWORD),
            'remember_token' => Str::random(10),
            'company_logo' => fake()->filePath(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }

    public function withAvatar(?string $path = null): static
    {
        return $this->state(fn (array $attributes) => ['avatar' => $path ?? fake()->filePath()]);
    }

    public function withCompanyLogo(string $path): static
    {
        return $this->state(fn (array $attributes) => ['company_logo' => $path]);
    }
}
