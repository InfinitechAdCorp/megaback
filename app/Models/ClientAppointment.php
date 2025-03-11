<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'property_name',
        'name',
        'email',
        'contact_number',
        'date',
        'message',
        'status',
        'type',
    ];
}
