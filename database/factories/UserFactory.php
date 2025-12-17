<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password', // otomatis di-hash oleh mutator
            'remember_token' => Str::random(10),
            'username' => $this->faker->userName(),
            'nim' => $this->faker->numerify('##########'),
            'kelas' => $this->faker->randomElement(['IF-45-01', 'IF-45-02', 'IF-45-03']),
            'phone' => $this->faker->phoneNumber(),
            'avatar_url' => $this->faker->imageUrl(200, 200, 'people', true),
            'role_id' => 3, // default customer
        ];
    }
}
