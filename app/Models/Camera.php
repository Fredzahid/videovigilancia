<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    protected $fillable = [
        'name',
        'stream_url',
        'rtsp_url',
        'location',
        'status',
        'description',
    ];
}
