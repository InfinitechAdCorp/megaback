<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'location',
        'status',
        'priceRange',
        'lotArea',
        'units',
        'amenities',
        'features', // ✅ Add features to fillable
        'masterPlan',
        'developmentType',
        'floors',
        'parkingLots',
        'specificLocation',
    ];

    protected $casts = [
        'amenities' => 'array',
        'units' => 'array',
        'features' => 'array', // ✅ Cast features as array
    ];
}
