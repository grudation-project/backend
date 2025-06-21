<?php

namespace Modules\Auth\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Auth\Enums\AuthEnum;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Manager\Models\Manager;
use Modules\Service\Models\Section;
use Modules\Service\Models\Service;
use Modules\Technician\Models\Technician;

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = UserTypeEnum::availableTypes();
        $services = Service::query()->pluck('id')->toArray();
        $sections = Section::query()->pluck('id')->toArray();

        foreach ($userTypes as $type) {
            $alphaType = UserTypeEnum::alphaTypes()[$type];

            $user = User::create([
                'name' => $alphaType,
                'email' => "$alphaType@dmu.edu.eg",
                'phone' => fake()->phoneNumber(),
                'status' => true,
                AuthEnum::VERIFIED_AT => now(),
                'password' => $alphaType,
                'type' => $type,
            ]);

            switch ($user->type) {
                case UserTypeEnum::MANAGER:
                    Manager::query()->create([
                        'user_id' => $user->id,
                        'service_id' => $services[array_rand($services)],
                    ]);
                    break;
                case UserTypeEnum::TECHNICIAN:
                    Technician::query()->create([
                        'user_id' => $user->id,
                        'manager_id' => 1,
                        'section_id' => $sections[array_rand($sections)],
                    ]);
                    break;
            }
        }
    }
}
