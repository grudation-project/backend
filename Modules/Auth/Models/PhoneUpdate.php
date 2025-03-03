<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneUpdate extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'code', 'expires_at', 'dial_code_length', 'user_id'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
