<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'image',
        'description',
        'email',
        'phone',
        'facebook',
        'instagram',
        'certificates',
        'gallery',
    ];

    protected $casts = [
        'certificates' => 'array',
        'gallery' => 'array',
    ];
}
