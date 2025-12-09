<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartureRequest extends Model
{
    //

    protected $fillable = [
        'student_id',
        'request_date',
        'reason',
        'status',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }


}
