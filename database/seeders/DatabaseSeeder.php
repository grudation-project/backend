<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Database\Seeders\AuthDatabaseSeeder;
use Modules\Service\Database\Seeders\ServiceDatabaseSeeder;
use Modules\Ticket\Database\Seeders\TicketDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AuthDatabaseSeeder::class,
            ServiceDatabaseSeeder::class,
            TicketDatabaseSeeder::class,
        ]);
    }
}
