<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    // Define the table name explicitly (optional, but good practice)
    protected $table = 'status';

    // Allow mass assignment for the 'name' field
    protected $fillable = ['name'];
}
