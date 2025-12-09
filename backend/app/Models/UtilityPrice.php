<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityPrice extends Model
{
    //

    protected $fillable = [
        'electricity_price',
        'water_price',
    ];

}
