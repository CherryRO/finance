<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CentreCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $centersOfCost = [
            'Sameday BT',
            'Sameday Iasi 1',
            'Sameday Iasi 2',
            'Sameday Iasi 3',
            'Service Botosani',
            'Service Iasi',
            'Anglia - Botosani',
            'Anglia - Oradea',
            'Anglia - Sibiu',
            'Anglia - Bucuresti',
            'Birouri Botosani',
            'Energy',
        ];

        foreach ($centersOfCost as $center) {
            DB::table('centers_of_cost')->insert([
                'name' => $center,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
