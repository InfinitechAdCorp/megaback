<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OngoingInfrastructure extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'title',
        'description',
        'image',
        'date',
    ];

    // Optionally, define hidden fields to exclude them from JSON responses
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    // Define date format for any date fields
    protected $dates = [
        'date', // Assuming 'date' is a DateTime column
    ];
}