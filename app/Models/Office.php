<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'location',
        'status',
        'price',
        'lotArea',
        'amenities',
    ];

    protected $casts = [
        'amenities' => 'array',
    ];
}
