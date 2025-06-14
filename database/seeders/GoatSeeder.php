<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Goat;
use App\Models\WeightLog;
use App\Models\FeedingLog;

class GoatSeeder extends Seeder
{
    public function run(): void
    {
        $breeds = ['Boer', 'Kiko', 'Nubian', 'Alpine', 'Local'];

        for ($i = 1; $i <= 50; $i++) {
            $goat = Goat::create([
                'tag_number' => 'G' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => fake()->optional()->firstName(),
                'breed' => fake()->randomElement($breeds),
                'gender' => fake()->randomElement(['male', 'female']),
                'date_of_birth' => fake()->dateTimeBetween('-3 years', '-6 months'),
                'status' => fake()->randomElement(['active', 'active', 'active', 'sold', 'dead']),
                'type' => fake()->randomElement(['fattening', 'breeding']),
                'origin' => fake()->randomElement(['bought', 'born']),
                'purchase_price' => fake()->optional()->randomFloat(2, 100, 500),
                'current_weight' => fake()->randomFloat(2, 20, 80),
                'notes' => fake()->optional()->sentence(),
            ]);

            // Add some weight logs
            for ($j = 0; $j < rand(1, 5); $j++) {
                WeightLog::create([
                    'goat_id' => $goat->id,
                    'weight' => fake()->randomFloat(2, 15, 85),
                    'weigh_date' => fake()->dateTimeBetween('-6 months', 'now'),
                    'notes' => fake()->optional()->sentence(),
                ]);
            }

            // Add some feeding logs
            for ($j = 0; $j < rand(5, 15); $j++) {
                FeedingLog::create([
                    'goat_id' => $goat->id,
                    'feed_type' => fake()->randomElement(['grass', 'concentrate', 'hay', 'mixed']),
                    'quantity' => fake()->randomFloat(2, 1, 10),
                    'cost' => fake()->randomFloat(2, 5, 50),
                    'feeding_date' => fake()->dateTimeBetween('-3 months', 'now'),
                    'is_group_feeding' => false,
                ]);
            }
        }
    }
}
