<?php

namespace Modules\Service\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Service\Models\Section;
use Modules\Service\Models\Service;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = Service::query()->pluck('id')->toArray();

        for ($i = 0; $i < 2; $i++) {
            Section::query()->create([
                'name' => 'اجهزه محمول',
                'service_id' => $services[array_rand($services)]
            ]);
        }
    }
}
