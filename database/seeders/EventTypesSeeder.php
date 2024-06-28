<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventType;

class EventTypesSeeder extends Seeder
{
    public function run()
    {
        $eventTypes = [
            ['name' => 'login', 'description' => 'User logged in'],
            ['name' => 'logout', 'description' => 'User logged out'],
            ['name' => 'delete_user', 'description' => 'User account deleted'],
            ['name' => 'failed_login', 'description' => 'User failed to log in'],
            // Adaugă alte tipuri de evenimente necesare
        ];

        foreach ($eventTypes as $eventType) {
            EventType::create($eventType);
        }
    }
}
