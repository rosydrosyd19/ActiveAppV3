<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemAuthFaceData extends Model
{
    use HasFactory;

    protected $table = 'system_auth_face_data';

    protected $fillable = [
        'user_id',
        'descriptor',
    ];

    protected $casts = [
        'descriptor' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
