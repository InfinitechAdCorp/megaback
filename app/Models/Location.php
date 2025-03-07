<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    // Define the fillable attributes to prevent mass assignment issues
    protected $fillable = [
        'name',
    ];
}
