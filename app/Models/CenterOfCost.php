<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterOfCost extends Model
{
  use HasFactory;

  protected $table = 'centers_of_cost'; // Specifică numele tabelei corect

  protected $fillable = ['name'];
}
