<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Defaults to creating a new user if not provided
            'name' => fake()->name(),
            'fathers_name' => fake()->name('male'),
            'mothers_name' => fake()->name('female'),
            'nid_number' => fake()->unique()->numerify('##########'),
            'nid_file' => 'uploads/nids/sample_nid.pdf',
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_number' => fake()->phoneNumber(),
            'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
            'religion' => fake()->randomElement(['Islam', 'Hinduism', 'Christianity', 'Buddhism']),
            'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
            'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'marital_status' => fake()->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'picture' => 'uploads/pictures/default.png',
        ];
    }
}