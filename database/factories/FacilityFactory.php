<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'Facility ' . fake()->name(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->country(),
            'zip' => fake()->randomNumber(5),
            'contact' => fake()->phoneNumber(),
        ];
    }
}
