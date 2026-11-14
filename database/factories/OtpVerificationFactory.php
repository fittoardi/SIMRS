<?php

namespace Database\Factories;

use App\Models\OtpVerification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<OtpVerification>
 */
class OtpVerificationFactory extends Factory
{
    protected $model = OtpVerification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone' => '628'.fake()->numerify('##########'),
            'otp_hash' => Hash::make('123456'),
            'action' => 'register',
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
            'verified_at' => null,
        ];
    }

    /**
     * State verified OTP.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => now(),
        ]);
    }
}
