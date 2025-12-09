<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable = [
        'user_id',
        'room_id',
        'student_code',
        'full_name',
        'phone',
        'gender',
        'date_of_birth',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function parent_students()
    {
        return $this->hasMany(ParentStudent::class, 'student_id', 'id');
    }

    public function departure_requests()
    {
        return $this->hasMany(DepartureRequest::class, 'student_id', 'id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'student_id', 'id');
    }

    // public function contract()
    // {
    //     return $this->hasOne(Contract::class, 'student_id', 'id');
    // }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'id');
    }

    public function parentUser()
    {
        return $this->hasOneThrough(
            // Model cuối cùng bạn muốn lấy
            User::class,
            // Model trung gian
            ParentStudent::class,
            // Khóa ngoại trên Model trung gian (ParentStudent) trỏ về Student
            'student_id',
            // Khóa ngoại trên Model cuối cùng (User) trỏ về ParentStudent
            'id',
            // Khóa chính cục bộ trên Student
            'id',
            // Khóa cục bộ trên ParentStudent trỏ về User (parent_students.user_id)
            'user_id'
        );
    }



    protected static function booted()
    {
        static::deleting(function ($student) {
            if ($student->user) {
                $student->user->delete();
            }
        });
    }
}
