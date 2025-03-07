<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',       // YouTube link
        'file_path', // Uploaded video file
        'thumbnail', // Custom thumbnail
        'location',
        'date',
        'views',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
