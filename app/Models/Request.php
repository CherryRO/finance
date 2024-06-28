<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'contact_number',
        'priority',
        'suma_necesara',
        'center_of_cost_id',
        'detalii_plata',
        'payment_method',
        'card',
        'status',
        'observatii_financiar',
        'observatii_solicitant'
    ];
}
