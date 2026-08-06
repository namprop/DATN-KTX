<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
     protected $primaryKey = 'payment_id';
    //

    protected $fillable = [
        'room_id',
        'student_id',
        'payment_id',
        'payment_code',
        'electricity_usage',
        'water_usage',
        'total_amount',
        'payment_status',
        'vnp_transaction_no',
        'payment_date',
        'description',
        'month',
        'year',
    ];


    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'payment_status' => PaymentStatus::class,
            'payment_date' => 'datetime',
        ];
    }
}
