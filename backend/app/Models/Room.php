<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //

    protected $fillable = [
        'room_code',
        'capacity',
        'status',
        'description',
        'price'
    ];

    public function students(){
        return $this->hasMany(Student::class, 'room_id', 'id');
    }

    public function facilites(){
        return $this->hasMany(Facilities::class, 'room_id', 'id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'room_id', 'id');
    }
}
