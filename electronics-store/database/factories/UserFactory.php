<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),               
            'password' => Hash::make('password'),       
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,               
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ];
    }
}
