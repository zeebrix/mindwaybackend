<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SleepScreen extends Model
{
    use HasFactory;
    protected $fillable = ['sleep_audio','audio_title','image','duration','total_play'];
}
