<?php

namespace Database\Factories;

use App\Models\Education;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    protected $model = Education::class;

    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'degree' => fake()->randomElement(['SSC', 'HSC', 'BSc', 'MSc', 'MBA']),
            'institute' => fake()->company() . ' High School & College',
            'board' => fake()->randomElement(['Dhaka', 'Chattogram', 'Rajshahi', 'Cumilla', 'Technical']),
            'grade' => fake()->randomElement(['A+', 'A', 'A-', 'B', '3.50', '3.85', '4.00']),
            'passing_year' => fake()->year(),
            'certificate' => 'uploads/certificates/sample.pdf',
        ];
    }
}