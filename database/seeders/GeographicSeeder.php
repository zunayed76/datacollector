<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Database\Seeder;

class GeographicSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            'Dhaka' => ['Dhaka', 'Gazipur', 'Narayanganj'],
            'Chattogram' => ['Chattogram', 'Cox\'s Bazar', 'Cumilla'],
            'Rajshahi' => ['Rajshahi', 'Bogra', 'Pabna'],
            'Khulna' => ['Khulna', 'Jashore', 'Satkhira'],
            'Barishal' => ['Barishal', 'Bhola', 'Patuakhali'],
            'Sylhet' => ['Sylhet', 'Moulvibazar', 'Habiganj'],
        ];

        foreach ($divisions as $divName => $districts) {
            // Create the Division using Factory with specific name
            $division = Division::factory()->create(['name' => $divName]);

            foreach ($districts as $distName) {
                // Create District linked to Division
                $district = District::factory()->create([
                    'division_id' => $division->id,
                    'name' => $distName
                ]);

                // Create 3 random Thanas for each District using Factory
                Thana::factory()->count(3)->create([
                    'district_id' => $district->id
                ]);

            }
        }
    }
}