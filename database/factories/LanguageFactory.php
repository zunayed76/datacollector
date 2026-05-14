<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'language_name' => fake()->randomElement(['English', 'Bengali', 'Arabic', 'French']),
            'proficiency_level' => fake()->randomElement(['Beginner', 'Intermediate', 'Fluent', 'Native']),
        ];
    }
}