<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProperty extends Model {
    use HasFactory;

    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'number',
        'property_name',
        'development_type',
        'unit_type',
        'price',
        'location',
        'images',
        'status'
    ];

    protected $casts = [
        'unit_type' => 'array',
        'images' => 'array',
    ];
}
