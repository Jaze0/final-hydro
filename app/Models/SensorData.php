<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    // Add the $fillable property to allow mass assignment
    protected $fillable = [
        'temperature',
        'humidity',
        'soil_moisture',
        'water_level',
    ];

    // Optionally, if your table is not the default "sensor_data", you can set the table name like this:
    // protected $table = 'sensor_data'; 
    
    // If you don't want to automatically manage the timestamps (created_at, updated_at), set this:
    // public $timestamps = false;
}

