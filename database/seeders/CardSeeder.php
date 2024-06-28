<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cards = [
            [
                'bank' => 'Bank 1',
                'cardholder_name' => 'John Doe',
                'first_four_digits' => '1234',
                'last_four_digits' => '5678',
                'expiry_date' => '12/25',
                'status' => '1', // Activ
                'observatii' => 'Card de test 1'
            ],
            [
                'bank' => 'Bank 2',
                'cardholder_name' => 'Jane Smith',
                'first_four_digits' => '2345',
                'last_four_digits' => '6789',
                'expiry_date' => '11/24',
                'status' => '1', // Activ
                'observatii' => 'Card de test 2'
            ],
            [
                'bank' => 'Bank 3',
                'cardholder_name' => 'Alice Johnson',
                'first_four_digits' => '3456',
                'last_four_digits' => '7890',
                'expiry_date' => '10/23',
                'status' => '2', // Inactiv
                'observatii' => 'Card de test 3'
            ],
            [
                'bank' => 'Bank 4',
                'cardholder_name' => 'Bob Brown',
                'first_four_digits' => '4567',
                'last_four_digits' => '8901',
                'expiry_date' => '09/22',
                'status' => '3', // Sters
                'observatii' => 'Card de test 4'
            ],
            [
                'bank' => 'Bank 5',
                'cardholder_name' => 'Charlie Davis',
                'first_four_digits' => '5678',
                'last_four_digits' => '9012',
                'expiry_date' => '08/21',
                'status' => '4', // Expirat
                'observatii' => 'Card de test 5'
            ],
            [
                'bank' => 'Bank 6',
                'cardholder_name' => 'Diana Evans',
                'first_four_digits' => '6789',
                'last_four_digits' => '0123',
                'expiry_date' => '07/20',
                'status' => '5', // Pierdut/Furat
                'observatii' => 'Card de test 6'
            ],
            [
                'bank' => 'Bank 7',
                'cardholder_name' => 'Frank Green',
                'first_four_digits' => '7890',
                'last_four_digits' => '1234',
                'expiry_date' => '06/19',
                'status' => '1', // Activ
                'observatii' => 'Card de test 7'
            ],
            [
                'bank' => 'Bank 8',
                'cardholder_name' => 'Grace Hill',
                'first_four_digits' => '8901',
                'last_four_digits' => '2345',
                'expiry_date' => '05/18',
                'status' => '2', // Inactiv
                'observatii' => 'Card de test 8'
            ],
            [
                'bank' => 'Bank 9',
                'cardholder_name' => 'Henry Irwin',
                'first_four_digits' => '9012',
                'last_four_digits' => '3456',
                'expiry_date' => '04/17',
                'status' => '3', // Sters
                'observatii' => 'Card de test 9'
            ],
            [
                'bank' => 'Bank 10',
                'cardholder_name' => 'Ivy Jackson',
                'first_four_digits' => '0123',
                'last_four_digits' => '4567',
                'expiry_date' => '03/16',
                'status' => '4', // Expirat
                'observatii' => 'Card de test 10'
            ],
        ];

        foreach ($cards as $card) {
            DB::table('cards')->insert([
                'bank' => $card['bank'],
                'cardholder_name' => $card['cardholder_name'],
                'first_four_digits' => $card['first_four_digits'],
                'last_four_digits' => $card['last_four_digits'],
                'expiry_date' => $card['expiry_date'],
                'status' => $card['status'],
                'observatii' => $card['observatii'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
