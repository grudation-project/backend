<?php

namespace Modules\Service\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Service\Models\Service;

class ServiceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0; $i<20; $i++) {
            Service::query()->create([
                'name' => fake()->name(),
            ]);
        }
    }
}
