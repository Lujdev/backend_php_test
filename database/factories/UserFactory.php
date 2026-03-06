<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

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
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'role' => fake()->randomElement(['admin', 'organizer', 'customer']),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state(['role' => 'admin']);
    }

    public function organizer()
    {
        return $this->state(['role' => 'organizer']);
    }

    public function customer()
    {
        return $this->state(['role' => 'customer']);
    }
}
