<?php

namespace Modules\Ticket\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Manager\Models\Manager;
use Modules\Service\Models\Service;
use Modules\Technician\Models\Technician;
use Modules\Ticket\Enums\TicketStatusEnum;
use Modules\Ticket\Models\Ticket;

class TicketDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $managers = Manager::query()->pluck('id')->toArray();
        $technicians = Technician::query()->pluck('id')->toArray();
        $services = Service::query()->pluck('id')->toArray();
        $clients = User::query()->where('type', UserTypeEnum::USER)->pluck('id')->toArray();

        for($i = 0; $i<20; $i++) {
            $status = fake()->randomELement(TicketStatusEnum::toArray());

            Ticket::query()->create([
                'title' => fake()->sentence(),
                'description' => fake()->paragraph(),
                'status' => $status,
                'service_id' => fake()->randomElement($services),
                'manager_id' => fake()->randomElement($managers),
                'technician_id' => $status == TicketStatusEnum::PENDING ? null : fake()->randomElement($technicians),
                'user_id' => fake()->randomElement($clients),
            ]);
        }
    }
}
