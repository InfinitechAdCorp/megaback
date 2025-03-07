<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'title',
        'description',
        'image',
        'file', // Add file field for video
        'media_type', // Add media type to differentiate image and video
        'date',
    ];

    // Optionally, you can define hidden fields to exclude them from being included in JSON responses.
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // You can also define date format for any date fields.
    protected $dates = [
        'date', // Assuming 'date' is a DateTime column
    ];

    // Optionally, you can add accessors to return the relative paths if needed
    public function getImageUrlAttribute()
    {
        return $this->image ? url('public/' . $this->image) : null;
    }

    public function getFileUrlAttribute()
    {
        return $this->file ? url('public/' . $this->file) : null;
    }
}
