<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosAudio extends Model
{
    use HasFactory;
    protected $fillable = ['sos_audio','session_id','audio_title','duration','total_play'];
}
