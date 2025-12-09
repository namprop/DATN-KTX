<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facilities extends Model
{
    //

    protected $fillable = [
        'room_id',
        'facility_code',
        'facility_name',
        'quantity',
        'is_student_device',
        'status',
    ];


    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
