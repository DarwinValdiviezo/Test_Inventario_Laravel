<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVisibleToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 