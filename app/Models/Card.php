<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank',
        'cardholder_name',
        'first_four_digits',
        'last_four_digits',
        'expiry_date',
        'status',
        'observatii'
    ];
}
