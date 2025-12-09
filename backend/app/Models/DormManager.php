<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DormManager extends Model
{
    //

    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected static function booted()
    {
        static::deleting(function ($dormManager) {
            if ($dormManager->user) {
                $dormManager->user->delete();
            }
        });
    }

}
