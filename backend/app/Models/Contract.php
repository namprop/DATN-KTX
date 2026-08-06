<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //
    protected $fillable = [
        'student_id',
        'start_date',
        'deposit',
        'end_date',
        'status',
    ];

    public function student(){
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    protected function casts(): array
    {
        return ['status' => ContractStatus::class];
    }
}
