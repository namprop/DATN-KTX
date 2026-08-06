<?php

namespace App\Models;

use App\Enums\DepartureRequestStatus;
use Illuminate\Database\Eloquent\Model;

class DepartureRequest extends Model
{
    //

    protected $fillable = [
        'student_id',
        'request_date',
        'reason',
        'status',
        'approved_at',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'status' => DepartureRequestStatus::class,
            'approved_at' => 'datetime',
        ];
    }


}
