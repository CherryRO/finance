<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Apelează seederul pentru a crea rolul de admin și utilizatorul
        $this->call(AdminRoleSeeder::class);

        // Apelează seederul pentru a crea tipurile de evenimente
        $this->call(EventTypesSeeder::class);

        // Apelează seederul pentru a crea permisiunile
        $this->call(PermissionsSeeder::class);

        // Apelează seederul pentru a crea permisiunile
        $this->call(CardSeeder::class);

        // Apelează seederul pentru a crea permisiunile
        $this->call(CentreCostSeeder::class);
    }
}
