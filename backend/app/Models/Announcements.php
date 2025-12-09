<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcements extends Model
{
    // Các trường có thể gán hàng loạt
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
        'status',
        'type',
    ];

    /**
     * Quan hệ với user
     * Một thông báo thuộc về một user (người tạo)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
