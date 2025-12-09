<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentStudent extends Model
{
    //

    protected $fillable = [
        'user_id',
        'student_id',
        'phone',
        'gender',
        'address',
        'full_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    protected static function booted()
    {
        static::deleting(function ($parentStudent) {
            if ($parentStudent->user) {
                return $parentStudent->user->delete();
            }
        });
    }
}
