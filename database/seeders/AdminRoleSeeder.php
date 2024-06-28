<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Creează rolul de admin
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);

        // Asociază toate permisiunile existente rolului de admin
        $permissions = Permission::all();
        $roleAdmin->syncPermissions($permissions);

        // Creează sau găsește utilizatorul
        $user = User::firstOrCreate(
            ['email' => 'ciresmihaigabriel@yahoo.com'],
            ['name' => 'Cires Mihai Gabriel', 'password' => bcrypt('password')] // Asigură-te că parola este setată corespunzător
        );

        if ($user) {
            $user->assignRole($roleAdmin);
            $this->command->info("Rolul de admin și toate permisiunile au fost atribuite utilizatorului cu email-ul ciresmihaigabriel@yahoo.com.");
        } else {
            $this->command->warn("Utilizatorul cu email-ul ciresmihaigabriel@yahoo.com nu a putut fi creat sau găsit. Rolul nu a fost atribuit.");
        }
    }
}
